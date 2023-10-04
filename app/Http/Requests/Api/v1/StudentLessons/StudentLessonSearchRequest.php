<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Material;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentLessonSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::SEE_LESSONS);
    }

    public function rules(): array
    {
        return [
            'content' => [
                'nullable',
                'string',
            ],
            'limit' => [
                'integer',
                'min:0'
            ],
        ];
    }

    public function queryParameters()
    {
        return [
            'content' => [
                'description' => 'Search by substring match (name, dni)',
                'example' => '',
            ],
            'limit' => [
                'description' => 'Limit of records returned',
                'example' => 10,
            ],
        ];
    }
}