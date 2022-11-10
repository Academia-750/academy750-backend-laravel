<?php

namespace App\Http\Requests\Api\v1\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ActionForMassiveSelectionTopicsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }

    public function messages(): array {
        return [
            //
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            //'email' => 'Correo Electrónico',
        ];
    }
}
