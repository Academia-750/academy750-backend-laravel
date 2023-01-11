<?php

namespace App\Http\Requests\Api\v1\Questions;

use App\Rules\Api\v1\Question\IsRequiredAnyReasonTextOrImageQuestionRule;
use App\Rules\Api\v1\Questions\IsRequiredAnyTypeTestQuestionRule;
use App\Rules\Api\v1\Questions\OnlyOneAnswerCorrectRule;
use App\Rules\Api\v1\Questions\IsThereShouldBeNoMoreThan1GroupingAnswer;
use App\Rules\Api\v1\Questions\ValidateImageQuestionRule;
use Illuminate\Foundation\Http\FormRequest;
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
            'is-question-true-or-false' => $this->get('is-question-true-or-false') === 'true',
            'is-correct-answer-true' => $this->get('is-correct-answer-true') === 'true',
            'is-correct-answer-false' => $this->get('is-correct-answer-false') === 'true',
        ]);
    }

    public function rules(): array
    {

        $isThereShouldBeNoMoreThan1GroupAnswer = collect([
            [
                'is-grouper' => (bool) $this->get('is-grouper-answer-correct')
            ],
            [
                'is-grouper' => (bool) $this->get('is-grouper-answer-one')
            ],
            [
                'is-grouper' => (bool) $this->get('is-grouper-answer-two')
            ],
            [
                'is-grouper' => (bool) $this->get('is-grouper-answer-three')
            ],
        ])->where('is-grouper', true)
        ->count() <= 1;


        return [
            'is-question-true-or-false' => ['required', 'boolean'],
            'question-text' => ['required', 'max:255',
                new IsThereShouldBeNoMoreThan1GroupingAnswer($isThereShouldBeNoMoreThan1GroupAnswer),
                new IsRequiredAnyTypeTestQuestionRule((bool) $this->get('is-test'), (bool) $this->get('is-card-memory')),
                new IsRequiredAnyReasonTextOrImageQuestionRule((bool) $this->get('is-card-memory'), $this->get('reason-question') , $this->file('file-reason')),
                new OnlyOneAnswerCorrectRule(
                    (bool) $this->get('is-question-true-or-false'),
                    (bool) $this->get('is-correct-answer-true'),
                    (bool) $this->get('is-correct-answer-false'),
                )
            ],
            'is-test' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],
            'is-card-memory' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],
            'is-visible' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],

            'answer-correct' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'max:255'])
            ],
            'is-grouper-answer-correct' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],

            'answer-one' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'max:255'])
            ],
            'is-grouper-answer-one' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],

            'answer-two' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'max:255'])
            ],
            'is-grouper-answer-two' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],

            'answer-three' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'max:255'])
            ],
            'is-grouper-answer-three' => [
                Rule::when((bool) !$this->get('is-question-true-or-false'), ['required', 'boolean'])
            ],

            'is-correct-answer-true' => [
                Rule::when(
                $this->get('is-question-true-or-false'),
                ['required', 'boolean'])
            ],
            'is-correct-answer-false' => [Rule::when(
                $this->get('is-question-true-or-false'),
                ['required', 'boolean'])
            ],

            'reason-question' => [
                'nullable',
                Rule::when(
                    (bool) $this->get('reason-question'), [
                    'required', 'max:400'
                ])
            ],
            'file-reason' => [
                'nullable',
                Rule::when((bool) $this->get('file-reason'), [
                    'required', new ValidateImageQuestionRule($this->file('file-reason'))
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
