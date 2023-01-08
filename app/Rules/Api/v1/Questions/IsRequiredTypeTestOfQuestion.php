<?php

namespace App\Rules\Api\v1\Questions;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class IsRequiredTypeTestOfQuestion implements Rule
{
    public function __construct(Public $typeTestNameRequired, Public $typeTestNameNotRequired, Public $valueOtherTypeTest)
    {
        //
    }

    /**
     * Valida si al menos se ha enviado el tipo de test actual o el otro tipo de test alternativo
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $value || $this->valueOtherTypeTest;
    }

    public function message(): string
    {
        return "La pregunta requiere al menos ser {$this->typeTestNameRequired} o {$this->typeTestNameNotRequired}";
    }
}
