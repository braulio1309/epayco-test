<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Session;

class SoapService
{
    public function registerClient($document, $name, $email, $phone)
    {
        try {
            $user = User::create([
                'document' => $document,
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
        $user = User::where('document', $document)->where('phone', $phone)->first();

        if (!$user) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Usuario no encontrado.',
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


    // Similar methods for loadWallet, pay, confirmPayment, and checkBalance
}
