<?php

namespace App\Rules\Api\v1\Questions;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class IsRequiredAnyTypeTestQuestionRule implements Rule
{
    public function __construct(Public $valueTypeTest, Public $valueAnotherTypeTest)
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        return $this->valueTypeTest || $this->valueAnotherTypeTest;
    }

    public function message(): string
    {
        return 'La pregunta requiere al menos ser para un Test o Tarjeta de memoria';
    }
}
