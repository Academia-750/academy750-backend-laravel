<?php

namespace App\Http\Requests\Api\v1\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ChangeMyPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current-password' => ['required', 'current_password'],
            'password' => ['required', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])([A-Za-z\d$@$!%*?&]|[^ ]){8,15}$/', 'confirmed']
        ];
    }

    /*public function messages(): array {
        return [
        ];
    }*/

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'current-password' => 'Contraseña actual',
            'password' => 'Nueva contraseña',
            'password_confirmation' => 'Contraseña confirmada'
        ];
    }

    /*public function withValidator($validator)
        {
            // formatear errores
            $validator->after(function ($validator) {
                $validator->errors()->add('field', 'Something is wrong with this field!');
            });
        }*/
}
