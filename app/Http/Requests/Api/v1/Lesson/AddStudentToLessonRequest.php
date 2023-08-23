<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class AddStudentToLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'uuid',
            ],

        ];
    }

    public function bodyParameters()
    {
        return [
            'user_id' => [
                'description' => 'Single User UUID to join the lesson',
                "example" => 'uuid'
            ],
        ];
    }
}