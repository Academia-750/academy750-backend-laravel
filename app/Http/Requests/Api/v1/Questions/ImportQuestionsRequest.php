<?php

namespace App\Http\Requests\Api\v1\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ImportQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['filesQuestions' => "string[]", 'filesQuestions.*' => "string[]"])] public function rules(): array
    {
        return [
            'filesQuestions' => ['required', 'array', 'max:3'],
            'filesQuestions.*' => [
                'required',
                'mimes:csv,txt'
            ]
        ];
    }

    #[ArrayShape(['filesQuestions' => "string"])] public function attributes(): array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'filesQuestions' => 'Archivos CSV de los temas a importar',
        ];
    }
}
