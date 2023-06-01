<?php

namespace App\Http\Requests\Api\v1\Questionnaires;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opposition_id' => ['required', 'uuid', 'exists:oppositions,uuid'],
            'count_questions_for_test' => ['required', Rule::in(['25', '50', '100', '120'])],
            'test_type' => ['required', Rule::in(['test', 'card_memory'])],
            'topics_id' => ['required', 'array'],
            'topics_id.*' => ['required', 'uuid', 'exists:topics,uuid']
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'topics_id' => 'Tema',
            'count_questions_for_test' => 'Número de preguntas',
            'test_type' => 'El tipo de Test',
            'opposition_id' => 'La oposición'
        ];
    }
}
