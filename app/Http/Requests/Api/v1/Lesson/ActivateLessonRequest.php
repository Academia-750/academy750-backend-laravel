<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class ActivateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active' => [
                'required',
                'boolean',
            ]
        ];
    }
}