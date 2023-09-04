<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Material;
use Database\Seeders\Permissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentLessonListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permissions::SEE_LESSONS);
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