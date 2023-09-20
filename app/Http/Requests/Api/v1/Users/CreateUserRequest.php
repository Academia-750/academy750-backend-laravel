<?php

namespace App\Http\Requests\Api\v1\Users;

use App\Rules\Api\v1\ValidateCorrectDNISpain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dni' => [
                'required',
                'alpha_num', Rule::unique('users', 'dni'),
                new ValidateCorrectDNISpain()
            ],
            'first-name' => [
                'required',
                'min:3',
                'max:25',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/'
            ],
            'last-name' => [
                'required',
                'min:3',
                'max:25',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/'
            ],
            'phone' => [
                'required',
                'numeric',
                'regex:/^[6789]\d{8}$/', Rule::unique('users', 'phone')
            ],
            'email' => [
                'required',
                'email', Rule::unique('users', 'email')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.required' => 'Ingresa un n° de documento',
            'dni.alpha_num' => 'Comienza con  números y termina en letra',
            'dni.regex' => 'El DNI debe tener 8 números y 1 letra',
            'dni.unique' => 'Ya existe este n° documento',
            'first-name.required' => 'Ingresa al menos un nombre',
            'first-name.regex' => 'Solo se aceptan letras',
            'first-name.min' => 'Nombre muy corto',
            'first-name.max' => 'Nombre muy largo',
            'last-name.required' => 'Ingresa al menos un apellido',
            'last-name.regex' => 'Solo se aceptan letras',
            'last-name.min' => 'Apellido muy corto',
            'last-name.max' => 'Apellido muy largo',
            'phone.required' => 'Ingresa un telefono',
            'phone.numeric' => 'El telefono deben ser números',
            'phone.unique' => 'Este teléfono ya se encuentra registrado',
            'phone.regex' => 'No es un formato válido para un número de España',
            'email.required' => 'Ingresa un correo eléctronico',
            'email.email' => 'Dirección de correo no valida',
            'email.unique' => 'Ya existe esta direccion de correo',
        ];
    }

    #[ArrayShape(['dni' => "string", 'first-name' => "string", 'last-name' => "string", 'phone' => "string", 'email' => "string", 'roles' => "string"])] public function attributes(): array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'dni' => 'Documento Nacional de Identidad',
            'first-name' => 'Nombre',
            'last-name' => 'Apellidos',
            'phone' => 'Numero de teléfono',
            'email' => 'Correo electrónico',
            'roles' => 'Roles',
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