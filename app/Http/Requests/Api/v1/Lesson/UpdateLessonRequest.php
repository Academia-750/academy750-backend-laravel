<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'min:4',
                config('constants.string_request_regex')
            ],
            'description' => [
                'string',
                'min:4',
                'max:1000',
            ],
            'date' => [
                'date',
            ],
            'start_time' => [
                'date_format:H:i',
                'before:end_time',
                Rule::requiredIf(!!$this->end_time)
            ],
            'end_time' => [
                'date_format:H:i',
                'after:start_time',
                Rule::requiredIf(!!$this->start_time)
            ],
            'is_online' => [
                'boolean'
            ],
            'url' => [
                'string',
                'url'
            ],
        ];
    }
}