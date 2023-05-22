<?php

namespace App\Http\Requests\Api\v1\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FetchHistoryStatisticalDataGraphByStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'string', Rule::in(['last-month', 'last-three-months', 'total'])],
            'topics_id' => ['required', 'array'],
            'topics_id.*' => ['required', 'uuid', 'exists:topics,uuid'],
        ];
    }

    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'period' => 'Periodo de tiempo',
            'topics_id' => 'Temas'
        ];
    }
}
