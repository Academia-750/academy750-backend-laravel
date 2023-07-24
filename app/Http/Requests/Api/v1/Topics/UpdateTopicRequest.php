<?php

namespace App\Http\Requests\Api\v1\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateTopicRequest extends FormRequest
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
                Rule::when($this->get('name') !== null, [
                    'max:255'
                ])
            ],
            'topic-group-id' => [
                'nullable',
                Rule::when($this->get('topic-group-id') !== null, [
                    'max:255', 'uuid', 'exists:topic_groups,uuid'
                ])
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
            'name' => 'Nombre del tema',
            'topic-group-id' => 'Grupo de tema'
        ];
    }
}
