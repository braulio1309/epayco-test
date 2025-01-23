<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultBalanceRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir siempre la solicitud
    }

    public function rules()
    {
        return [
            'document' => 'required|file',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/', // Validación para un teléfono de 10-15 dígitos
        ];
    }

    public function messages()
    {
        return [
            'document.required' => 'El documento es obligatorio.',
            'phone.required' => 'El teléfono es obligatorio.',
        ];
    }
}
