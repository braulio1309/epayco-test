<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => 'required|exists:sessions,id',
            'token' => 'required|size:6',
        ];
    }

    public function messages():array
    {
        return [
            'session_id.required' => 'El ID de la sesión es obligatorio.',
            'session_id.exists' => 'El ID de la sesión no es válido.',
            'token.required' => 'El token de confirmación es obligatorio.',
            'token.size' => 'El token debe tener exactamente 6 dígitos.',
        ];
    }
}
