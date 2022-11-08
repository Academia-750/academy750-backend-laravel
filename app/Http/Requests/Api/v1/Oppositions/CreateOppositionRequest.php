<?php

namespace App\Http\Requests\Api\v1\Oppositions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateOppositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:100',
                Rule::unique('oppositions', 'name')
            ],
            'period' =>[
                'required',
                'max:100'
            ]
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
            'name' => 'Nombre de la oposición',
            'period' => 'Periodo de la oposición'
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
