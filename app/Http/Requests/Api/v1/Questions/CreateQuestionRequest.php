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
        ]);
    }

    public function rules(): array
    {
        return [

            'is-question-binary-alternatives' => [
                'required', Rule::in(['yes', 'no'])
            ],
            'is-visible' => ['required', 'boolean'],
            'question-text' => ['required', 'max:65535',
                new IsThereShouldBeNoMoreThan1GroupingAnswer(
                    (bool) $this->get('is-question-binary-alternatives'),
                    $this->get('is-grouper-answer-correct'),
                    $this->get('is-grouper-answer-one'),
                    $this->get('is-grouper-answer-two'),
                    $this->get('is-grouper-answer-three')
                ),
                new IsRequiredAnyTypeTestQuestionRule((bool) $this->get('is-test'), (bool) $this->get('is-card-memory')),
                new IsRequiredAnyReasonTextOrImageQuestionRule((bool) $this->get('is-card-memory'), $this->get('reason-question') , $this->file('file-reason'))
            ],
            'is-test' => ['required', 'boolean'],
            'is-card-memory' => ['required', 'boolean'],

            'answer-correct' => ['required', 'max:65535'],

            'is-grouper-answer-correct' => [
                Rule::when($this->get('
                is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'boolean'])
            ],

            'answer-one' => [
                Rule::when($this->get('is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'max:65535'])
            ],
            'is-grouper-answer-one' => [
                Rule::when($this->get('is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'boolean'])
            ],

            'answer-two' => [
                Rule::when($this->get('is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'max:65535'])
            ],
            'is-grouper-answer-two' => [
                Rule::when($this->get('is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'boolean'])
            ],

            'answer-three' => [
                Rule::when($this->get('is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'max:65535'])
            ],
            'is-grouper-answer-three' => [
                Rule::when($this->get('is-question-binary-alternatives') === 'no' && $this->get('is-test'), ['required', 'boolean'])
            ],

            'another-answer-binary-alternative' => [
                Rule::when(
                $this->get('is-question-binary-alternatives') === 'yes' && $this->get('is-test'),
                ['required', 'max:65535'])
            ],

            'reason-question' => [
                'nullable',
                Rule::when(
                    (bool) $this->get('reason-question'), [
                    'required', 'max:65535'
                ])
            ],
            'file-reason' => [
                'nullable',
                Rule::when((bool) $this->get('file-reason'), [
                    'required', new ValidateImageQuestionRule($this->file('file-reason'))
                ])
            ],

            "show_reason_text_in_test" => [
                'required', Rule::in(['yes', 'no'])
            ],
            "show_reason_text_in_card_memory" => [
                'required', Rule::in(['yes', 'no'])
            ],
            "show_reason_image_in_test" => [
                'required', Rule::in(['yes', 'no'])
            ],
            "show_reason_image_in_card_memory" => [
                'required', Rule::in(['yes', 'no'])
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
}
