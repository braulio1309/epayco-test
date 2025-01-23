<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\PaymentTokenMail;
use Illuminate\Support\Facades\Storage;
class SoapService
{
    
    public function registerClient($document, $name, $email, $phone)
    {
        try {

            // Generar un nombre único para el archivo (puedes usar un UUID o un timestamp)
            $uniqueFileName = Str::uuid() . '.' . $document->getClientOriginalExtension();

            // Guardar el archivo en el almacenamiento público con el nombre único
            $path = $document->storeAs('documents', $uniqueFileName, 'public');
            $user = User::create([
                'document' => $path,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ]);

            Wallet::create(['user_id' => $user->id]);

            return [
                'success' => true,
                'cod_error' => '00',
                'message_error' => 'Client registered successfully',
                'data' => $user,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    public function loadWallet($document, $phone, $value)
    {
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Usuario no encontrado.',
                'data' => null,
            ];
        }

        if(!$this->compareFiles($document, $user->document)){
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Su archivo no coincide.',
                'data' => null,
            ];
        }

        $wallet = $user->wallet;
        $wallet->balance += $value;
        $wallet->save();

        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Recarga exitosa.',
            'data' => ['balance' => $wallet->balance],
        ];
    }

    public function compareFiles($file, string $filePath2)
    {
        $uniqueFileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $uniqueFileName, 'public');
        if (Storage::disk('public')->exists('documents/' . $uniqueFileName) && Storage::disk('public')->exists($filePath2)) {
            $file1 = Storage::get($path) == Storage::get($filePath2);
            if($file1){
                Storage::delete($path);
                return true;
            }
        }

        return false;
    }

    private function generateToken()
    {
        return Str::random(6); // Genera un token aleatorio de 6 caracteres (puedes hacer ajustes si quieres que solo sea numérico)
    }

    private function sendTokenToEmail($userEmail, $token)
    {
        Mail::to($userEmail)->send(new PaymentTokenMail($token)); // Usa un mail que se encargue de enviar el token
    }

    public function pay($document, $phone, $amount)
    {
        // Verifica si el usuario tiene saldo suficiente
        $user = User::where('phone', $phone)->first();

        if (!$user->wallet) {
            return [
                'success' => false,
                'message_error' => 'El usuario no tiene una billetera registrada.',
                'cod_error' => '01'
            ];
        }

        if(!$this->compareFiles($document, $user->document)){
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Su archivo no coincide.',
                'data' => null,
            ];
        }

        $wallet =  $user->wallet;
        // Verifica si tiene suficiente saldo
        if ($wallet->balance < $amount) {
            return [
                'success' => false,
                'message_error' => 'Saldo insuficiente.',
                'cod_error' => '02'
            ];
        }

        // Genera un ID de sesión único
        $sessionId = Str::uuid();

        // Genera un token de 6 dígitos
        $token = $this->generateToken();

        // Crea una transacción pendiente
        Transaction::create([
            'session_id' => $sessionId,
            'user_id' => $wallet->user_id,
            'amount' => $amount,
            'status' => 'pending',
            'token' => $token,
            'type' => 'payment',
            'wallet_id' => $wallet->id,
            'expires_at' => Carbon::now()->addMinutes(10) // El token expira en 10 minutos
        ]);

        // Enviar el token por correo al usuario
        $this->sendTokenToEmail($wallet->user->email, $token);

        return [
            'success' => true,
            'message_error' => 'Se ha enviado el token al correo. Usa el ID de sesión para confirmar el pago.',
            'cod_error' => '00',
            'session_id' => $sessionId
        ];
    }

    public function confirmPayment($sessionId, $token)
    {
        // Busca la transacción correspondiente
        $transaction = Transaction::where('session_id', $sessionId)->where('status', 'pending')->first();

        if (!$transaction) {
            return [
                'success' => false,
                'message_error' => 'Transacción no encontrada o ya fue procesada.',
                'cod_error' => '03'
            ];
        }

        // Verifica si el token coincide
        if ($transaction->token !== $token) {
            return [
                'success' => false,
                'message_error' => 'El token no es válido.',
                'cod_error' => '04'
            ];
        }

        // Verifica si el token ha expirado
        if (Carbon::now()->greaterThan($transaction->expires_at)) {
            return [
                'success' => false,
                'message_error' => 'El token ha expirado.',
                'cod_error' => '05'
            ];
        }

        // Encuentra el usuario y actualiza el saldo de la billetera
        $wallet = Wallet::where('id', $transaction->wallet_id)->first();
        if ($wallet) {
            $wallet->balance -= $transaction->amount;
            $wallet->save();
        }

        // Actualiza el estado de la transacción a 'completed'
        $transaction->status = 'completed';
        $transaction->save();

        return [
            'success' => true,
            'message_error' => 'Pago realizado con éxito.',
            'cod_error' => '00'
        ];
    }

    public function consultBalance($document, $phone)
    {
        // Buscar el usuario con el documento y teléfono
        $user = User::where('phone', $phone)->first();

        if(!$this->compareFiles($document, $user->document)){
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Su archivo no coincide.',
                'data' => null,
            ];
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'cod_error' => '01', // Error, usuario no encontrado
                'message_error' => 'No se encontró un usuario con ese documento y teléfono.',
                'data' => []
            ]);
        }

        // Si se encuentra el usuario, retornar el saldo
        return response()->json([
            'success' => true,
            'cod_error' => '00', // Éxito
            'message_error' => 'Saldo consultado correctamente.',
            'data' => [
                'balance' => $user->wallet->balance
            ]
        ]);
    }




    // Similar methods for loadWallet, pay, confirmPayment, and checkBalance
}
