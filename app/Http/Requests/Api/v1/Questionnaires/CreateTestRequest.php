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
            'topics' => ['required', 'array'],
            'topics.*' => ['required', 'uuid', 'exists:topics,id']
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'topics' => 'Tema'
        ];
    }
}
