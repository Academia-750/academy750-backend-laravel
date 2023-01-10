<?php

namespace App\Rules\Api\v1\Question;

use App\Core\Resources\Questions\v1\Services\SaveQuestionsService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class IsRequiredAnyReasonTextOrImageQuestionRule implements Rule
{
    public function __construct(Public $isCardMemory, Public $reasonTextQuestion, Public $reasonImageQuestion)
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->isCardMemory) {
            return true;
        }

        return $this->reasonTextQuestion || !SaveQuestionsService::validateImageWithFails($this->reasonImageQuestion)->fails();
    }

    public function message(): string
    {
        return 'Para las tarjetas de memoria, es necesario proporcionar al menos una Explicación en Texto o Imagen';
    }
}
