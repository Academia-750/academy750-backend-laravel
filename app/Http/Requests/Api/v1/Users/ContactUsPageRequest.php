<?php

namespace App\Http\Requests\Api\v1\Users;

use App\Rules\Api\v1\ContactUs\RecaptchaValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ContactUsPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    #[ArrayShape(['reason' => "array", 'first-name' => "string[]", 'last-name' => "string[]", 'phone' => "array", 'email' => "array", 'message' => "string[]", 'g-recaptcha-response' => "string[]"])] public function rules(): array
    {
        return [
            'reason' => [
                'required', Rule::in(['general', 'inscription', 'reset-password'])
            ],
            'first-name' => [
                'required', 'min:3', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/'
            ],
            'last-name' => [
                'required', 'min:3', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/'
            ],
            'phone' => [
                'required', 'numeric', 'regex:/^[6789]\d{8}$/', Rule::unique('users', 'phone')
            ],
            'email' => [
                'required', 'email', Rule::when($this->get('reason') === 'inscription', [
                    Rule::unique('users', 'email')
                ])
            ],
            'message' => [
                'required', 'max:500'
            ],
            'g-recaptcha-response' => [
                'required', new RecaptchaValidationRule
            ]
        ];
    }

    #[ArrayShape(['reason' => 'string', 'first-name' => "string", 'last-name' => "string", 'phone' => "string", 'email' => "string", 'message' => "string"])]
    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'reason' => 'Motivo',
            'first-name' => 'Nombre',
            'last-name' => 'Apellidos',
            'phone' => 'Numero de teléfono',
            'email' => 'Correo electrónico',
            'message' => 'Mensaje'
        ];
    }
}
