<?php

namespace App\Http\Requests\Api\v1\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is-test' => $this->get('is-test') === 'true',
            'is-card-memory' => $this->get('is-card-memory') === 'true',
            'is-visible' => $this->get('is-visible') === 'true',
            'is-grouper-answer-correct' => $this->get('is-grouper-answer-correct') === 'true',
            'is-grouper-answer-one' => $this->get('is-grouper-answer-one') === 'true',
            'is-grouper-answer-two' => $this->get('is-grouper-answer-two') === 'true',
            'is-grouper-answer-three' => $this->get('is-grouper-answer-three') === 'true',
        ]);
    }

    public function rules(): array
    {
        return [
            'question-text' => ['required', 'max:255'],
            'is-test' => ['required', 'boolean'],
            'is-card-memory' => ['required', 'boolean'],
            'is-visible' => ['required', 'boolean'],

            'answer-correct' => ['required', 'max:255'],
            'is-grouper-answer-correct' => ['required', 'boolean'],

            'answer-one' => ['required', 'max:255'],
            'is-grouper-answer-one' => ['required', 'boolean'],

            'answer-two' => ['required', 'max:255'],
            'is-grouper-answer-two' => ['required', 'boolean'],

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
