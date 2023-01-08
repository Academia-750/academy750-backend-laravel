<?php

namespace App\Rules\Api\v1\Questions;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class IsThereShouldBeNoMoreThan1GroupingAnswer implements Rule
{
    public function __construct(Public $isThereShouldBeNoMoreThan1GroupAnswer)
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        return (bool) $this->isThereShouldBeNoMoreThan1GroupAnswer;
    }

    public function message(): string
    {
        return "Solo puedes marcar 1 respuesta agrupadora como máximo";
    }
}
