<?php

namespace App\Http\Requests\Api\v1\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClaimQuestionMailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'test_id' => ['required', 'uuid', 'exists:tests,id'],
            'question_id' => ['required', 'uuid', 'exists:questions,id'],
            'claim_text' => ['required', 'string', 'max:400'],
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'test_id' => 'Identificador del Test',
            'question_id' => 'Identificador de la pregunta',
            'claim_text' => 'Motivo de impugnacion',
        ];
    }
}
