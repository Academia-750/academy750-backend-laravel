<?php

namespace App\Http\Requests\Api\v1\Oppositions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateOppositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                Rule::when( $this->get('name') !== null ,
                    [
                        'max:100',
                        Rule::unique('oppositions', 'name')->ignore($this->route('opposition')?->getRouteKey())
                    ]
                )
            ],
            'period' => [
                'nullable',
                Rule::when( $this->get('period') !== null ,
                    [
                        'max:100'
                    ]
                )
            ],
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

    /*public function withValidator($validator)
        {
            // formatear errores
            $validator->after(function ($validator) {
                $validator->errors()->add('field', 'Something is wrong with this field!');
            });
        }*/
}
