<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoadWalletRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Convierte 'value' a número (float, por ejemplo)
        if ($this->has('value')) {
            $this->merge([
                'value' => is_numeric($this->value) ? (float) $this->value : 0
            ]);
        }
    }
    
    public function rules()
    {
        return [
            'document' => 'required|file',
            'phone' => 'required|exists:users,phone',
            'value' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'document.required' => 'El documento es obligatorio.',
            'document.exists' => 'El documento no coincide con ningún cliente.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.exists' => 'El número de teléfono no coincide con ningún cliente.',
            'value.required' => 'El valor a recargar es obligatorio.',
            'value.numeric' => 'El valor debe ser un número.',
            'value.min' => 'El valor mínimo para recargar es 1.',
        ];
    }
}