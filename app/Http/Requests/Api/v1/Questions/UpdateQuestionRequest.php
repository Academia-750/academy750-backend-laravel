<?php

namespace App\Http\Requests\Api\v1\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question-text' => ['required', 'max:255'],
            'is-test' => ['required', 'boolean'],
            'is-card-memory' => ['required', 'boolean'],
            'is-visible' => ['required', 'boolean'],

            'answer-correct-id' => ['required', 'uuid', 'exists:answers,id'],
            'answer-correct' => ['required', 'max:255'],
            'is-grouper-answer-correct' => ['required', 'boolean'],

            'answer-one-id' => ['required', 'uuid', 'exists:answers,id'],
            'answer-one' => ['required', 'max:255'],
            'is-grouper-answer-one' => ['required', 'boolean'],

            'answer-two-id' => ['required', 'uuid', 'exists:answers,id'],
            'answer-two' => ['required', 'max:255'],
            'is-grouper-answer-two' => ['required', 'boolean'],

            'answer-three-id' => ['required', 'uuid', 'exists:answers,id'],
            'answer-three' => ['required', 'max:255'],
            'is-grouper-answer-three' => ['required', 'boolean'],

            'reason-question' => [
                'nullable',
                Rule::when((bool) $this->get('is-card-memory') && (bool) !$this->get('file-reason'), [
                    'required', 'max:400'
                ])
            ],
            'file-reason' => [
                'nullable',
                Rule::when((bool) $this->get('is-card-memory') && (bool) !$this->get('reason-question'), [
                    'required', 'max:400'
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
            //'email' => 'Correo Electrónico',
        ];
    }
}
