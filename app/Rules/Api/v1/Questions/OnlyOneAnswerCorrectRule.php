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
        return true;
    }

    public function message(): string
    {
        return __('validation.error');
    }
}
