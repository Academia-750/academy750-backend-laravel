<?php

namespace App\Http\Requests\Api\v1\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ActionForMassiveSelectionUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "action" => [
                'required',
                'string',
                Rule::in(['delete', 'lock-account', 'unlock-account'])
            ],
            "users" => ['required', 'array'],
            "users.*" => ['uuid', 'distinct:strict', 'exists:users,uuid']
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'action' => 'Accion sobre múltiples registros',
            'users' => 'Los usuarios'
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
