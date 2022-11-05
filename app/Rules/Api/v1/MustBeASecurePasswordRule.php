<?php

namespace App\Rules\Api\v1;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class MustBeASecurePasswordRule implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        $regular_expression = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&#^)(])([A-Za-z\d$@$!%*?&#)(^]|[^ ]){8,15}$/";

        return preg_match($regular_expression, $value);
    }

    public function message(): string
    {
        return 'Esta no es una contraseña segura';
    }
}
