<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class StudentLessonListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::SEE_LESSONS);
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

    public function queryParameters()
    {
        return [
            'from' => [
                'description' => 'From Date ',
                'example' => '2023-02-01'
            ],
            'to' => [
                'description' => 'To Date (Including that date)',
                'example' => '2023-02-05'
            ],
            'content' => [
                'description' => 'Search by substring match (name, description)',
                'example' => '',
            ],
        ];
    }
}