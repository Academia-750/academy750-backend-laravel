<?php

namespace App\Http\Requests\Api\v1\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignOppositionWithSubtopicsToTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opposition-id' => [
                'required', 'exists:oppositions,uuid'
            ],
            'subtopics' => [
                'nullable',
                Rule::when($this->get('subtopics') !== null, [
                    'array'
                ])
            ]/*,
            "subtopics.*" => [
                'nullable',
                Rule::when($this->get('subtopics') !== null, [
                    'uuid', 'distinct:strict', 'exists:users,id'
                ]),
            ]*/
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'opposition-id' => 'Identificador de la Oposición',
            'subtopics' => 'Subtemas',
        ];
    }
}
