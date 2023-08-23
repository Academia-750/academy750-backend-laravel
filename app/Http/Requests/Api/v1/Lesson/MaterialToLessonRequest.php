<?php

namespace App\Http\Requests\Api\v1\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class MaterialToLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_id' => [
                'required',
                'integer',
                'min:0'

            ],

        ];
    }

    public function bodyParameters()
    {
        return [
            'material_id' => [
                'description' => 'Material Id',
                "example" => '23'
            ],
        ];
    }
}