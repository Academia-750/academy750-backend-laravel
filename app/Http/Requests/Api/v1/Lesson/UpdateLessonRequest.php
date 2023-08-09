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
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Lesson Name',
                "example" => 'Law Lesson 2'
            ],
            'date' => [
                'description' => 'Lesson Date',
                "example" => '2023-09-03'
            ],
            'start_time' => [
                'description' => 'Lesson Start Time',
                "example" => '10:00'
            ],
            'end_time' => [
                'description' => 'Lesson End Time',
                "example" => '12:00'
            ],
            'is_online' => [
                'description' => 'Boolean',
                "example" => false
            ],
            'url' => [
                'description' => 'For Online Lessons, lesson room url',
                "example" => 'https://zoom-api.com/roomName'
            ],
        ];
    }
}