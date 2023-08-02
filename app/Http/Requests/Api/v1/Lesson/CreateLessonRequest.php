<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class CreateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:4',
                config('constants.string_request_regex')

            ],
            'date' => [
                'required',
                'date',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'string',
                'date_format:H:i',
                'after:start_time',
            ],
        ];
    }
}