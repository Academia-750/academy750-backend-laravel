<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class CalendarLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => [
                'required',
                'date'
            ],
            'to' => [
                'required',
                'date',
                "after_or_equal:from"
            ],
            'content' => [
                'string',
            ]
        ];
    }
}