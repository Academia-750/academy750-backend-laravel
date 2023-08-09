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
                'description' => 'Lesson Name',
                "example" => '10:00'
            ],
            'end_time' => [
                'description' => 'Lesson Name',
                "example" => '12:00'
            ],
        ];
    }
}