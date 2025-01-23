<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('amount')) {
            $this->merge([
                'value' => is_numeric($this->value) ? (float) $this->value : 0
            ]);
        }
    }

    public function rules()
    {
        return [
            'document' => 'required|exists:users,document',
            'phone' => 'required|exists:users,phone',
            'amount' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'document.required' => 'El documento es obligatorio.',
            'document.exists' => 'El documento no coincide con ningún cliente.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.exists' => 'El número de teléfono no coincide con ningún cliente.',
            'amount.required' => 'El monto de la compra es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.min' => 'El monto debe ser al menos 1.',
        ];
    }
}
