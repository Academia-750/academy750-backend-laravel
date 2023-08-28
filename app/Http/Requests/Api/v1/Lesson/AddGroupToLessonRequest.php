<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class AddGroupToLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => [
                'required',
                'integer',
                'min:0'
            ],
        ];
    }

    public function bodyParameters()
    {
        return [
            'group_id' => [
                'description' => 'Group Code: All the active users of the group will join the lesson',
                "example" => 'BCDEFRT'
            ],
        ];
    }
}