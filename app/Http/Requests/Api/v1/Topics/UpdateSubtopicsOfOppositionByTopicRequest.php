<?php

namespace App\Http\Requests\Api\v1\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubtopicsOfOppositionByTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subtopics' => [
                'nullable',
                Rule::when($this->get('subtopics') !== null, [
                    'array'
                ])
            ]
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'subtopics' => 'Subtemas',
        ];
    }
}
