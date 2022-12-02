<?php

namespace App\Http\Requests\Api\v1\Subtopics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateRelationshipQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question-text' => [
                'required', 'max:255'
            ],
            'reason' => [
                'nullable',
                Rule::when($this->get('is_memory_card_test') !== null && $this->get('is_memory_card_test') === true, [
                    'required', 'max:350'
                ])
            ],
            'is_visible' => [
                'required', 'boolean'
            ],
            'is_test' => [
                'required', 'boolean'
            ],
            'is_memory_card_test' => [
                'required', 'boolean'
            ],
            'answers' => [
                'array', 'size:4'
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
            'question-text' => 'Pregunta',
            'is_visible' => 'Opción de visibilidad',
            'is_test' => 'Opción de si es Cuestionario',
            'is_memory_card_test' => 'Opción de si es Tarjeta de memoria',
            'answers' => 'Respuestas'
        ];
    }
}
