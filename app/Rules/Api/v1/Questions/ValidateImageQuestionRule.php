<?php

namespace App\Rules\Api\v1\Questions;

use App\Core\Resources\Questions\v1\Services\SaveQuestionsService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ValidateImageQuestionRule implements Rule
{
    public function __construct(Public $fileImageValidate = null)
    {
        //
    }

    public function passes($attribute, $value): bool
    {

        if ($this->fileImageValidate) {
            return SaveQuestionsService::validateImageWithFails($this->fileImageValidate)->fails();
        }

        return SaveQuestionsService::validateImageWithFails($value)->fails();
    }

    public function message(): string
    {
        return 'Proporciona una imagen válida para la pregunta';
    }
}
