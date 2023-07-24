<?php

namespace App\Http\Requests\Api\v1\Questionnaires;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResolveQuestionOfTestRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'uuid', 'exists:questions,uuid'],
            'test_id' => ['required', 'uuid', 'exists:tests,uuid'],
            'answer_id' => ['nullable', Rule::when((bool) $this->get('answer_id'), [
                'uuid', 'exists:answers,uuid'
            ] )],
            /*'test_id' => ['required', 'uuid', 'exists:tests,id'],
            'questions' => ['required', 'array'],
            'questions.*.question_id' => [
                'uuid', 'exists:questions,id'
            ],*/
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'question_id' => 'Pregunta',
            'test_id' => 'Test',
            'answer_id' => 'Respuesta',
        ];
    }
}
