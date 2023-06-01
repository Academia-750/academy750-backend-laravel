<?php

namespace App\Http\Requests\Api\v1\Profile;

use App\Rules\Api\v1\ValidateCorrectDNISpain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class UpdateDataProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['dni' => "array", 'first-name' => "string[]", 'last-name' => "string[]", 'phone' => "array", 'email' => "array"])] public function rules(): array
    {
        return [
            'first-name' => [
                'nullable',
                Rule::when( $this->get('first-name') !== null ,
                    [
                        'min:3',
                        'max:25',
                        'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/'
                    ]
                )
            ],
            'last-name' => [
                'nullable',
                Rule::when(
                    $this->get('last-name') !== null,
                    [
                        'min:3',
                        'max:25',
                        'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/'
                    ]
                )
            ],
            'phone' => [
                'nullable',
                Rule::when(
                    $this->get('phone') !== null,
                    [
                        'numeric',
                        'regex:/^[6789]\d{8}$/',
                        Rule::unique('users', 'phone')
                            ->ignore(
                                auth()->user()?->getRouteKey(),
                                auth()->user()?->getRouteKeyName()
                            )
                    ]
                )
            ],
            'email' => [
                'nullable',
                Rule::when(
                    $this->get('email') !== null,
                    [
                        'email',
                        Rule::unique('users', 'email')
                            ->ignore(
                                auth()->user()?->getRouteKey(),
                                auth()->user()?->getRouteKeyName()
                            )
                    ]
                )
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

    #[ArrayShape(['dni' => "string", 'first-name' => "string", 'last-name' => "string", 'phone' => "string", 'email' => "string"])] public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'dni' => 'Documento Nacional de Identidad',
            'first-name' => 'Nombre',
            'last-name' => 'Apellidos',
            'phone' => 'Numero de teléfono',
            'email' => 'Correo electrónico',
        ];
    }
}
