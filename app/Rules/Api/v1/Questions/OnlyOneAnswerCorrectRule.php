<?php

namespace App\Rules\Api\v1\Questions;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class OnlyOneAnswerCorrectRule implements Rule
{
    public function __construct(Public bool $isQuestionTrueOrFalse, Public bool $isCorrectAnswerTrue, Public bool $isCorrectAnswerFalse)
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->isQuestionTrueOrFalse) {
            return true;
        }

        $answers = collect([
            ['is-correct' => $this->isCorrectAnswerTrue],
            ['is-correct' => $this->isCorrectAnswerFalse],
        ]);

        return $answers->where('is-correct', true)->count() === 1;
    }

    public function message(): string
    {
        return 'En las preguntas de verdadero o falso debe tener 1 respuesta correcta';
    }
}
