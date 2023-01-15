<?php

namespace App\Rules\Api\v1\Questions;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class IsThereShouldBeNoMoreThan1GroupingAnswer implements Rule
{
    public function __construct(Public bool $isQuestionBinaryAlternatives, Public $isGrouperAnswerCorrect, Public $isGrouperAnswerOne, Public $isGrouperAnswerTwo, Public $isGrouperAnswerThree)
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if ($this->isQuestionBinaryAlternatives) {
            return true;
        }


        $isThereShouldBeNoMoreThan1GroupAnswer = collect([
                [
                    'is-grouper' => (bool) $this->isGrouperAnswerCorrect
                ],
                [
                    'is-grouper' => (bool) $this->isGrouperAnswerOne
                ],
                [
                    'is-grouper' => (bool) $this->isGrouperAnswerTwo
                ],
                [
                    'is-grouper' => (bool) $this->isGrouperAnswerThree
                ],
            ]);


        return $isThereShouldBeNoMoreThan1GroupAnswer->where('is-grouper', true)
                ->count() <= 1;
    }

    public function message(): string
    {
        return "Solo puedes marcar 1 respuesta agrupadora como máximo";
    }
}
