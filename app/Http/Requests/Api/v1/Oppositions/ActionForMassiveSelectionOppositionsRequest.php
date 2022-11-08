<?php

namespace App\Http\Requests\Api\v1\Oppositions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ActionForMassiveSelectionOppositionsRequest extends FormRequest
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
                Rule::in(['delete'])
            ],
            "oppositions" => ['required', 'array', 'min:2'],
            "oppositions.*" => ['uuid', 'distinct:strict', 'exists:oppositions,id']
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'action' => 'Acciones',
            'oppositions' => 'Oposiciones'
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
