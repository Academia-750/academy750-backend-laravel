<?php

namespace App\Rules\Api\v1\Questions;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class OnlyOneAnswerCorrectRule implements Rule
{
    public function __construct(Public bool $isQuestionBinaryAlternatives, Public bool $isCorrectAnswerTrue, Public bool $isCorrectAnswerFalse)
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->isQuestionBinaryAlternatives) {
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
        return 'En las preguntas binarias (de 2 alternativas) debe tener 1 respuesta correcta';
    }
}
