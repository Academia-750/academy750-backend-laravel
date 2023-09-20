<?php

namespace App\Http\Requests\Api\v1\StudentLessons;

use App\Models\Material;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentLessonMaterialListRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!$this->user()->can(Permission::SEE_LESSONS)) {
            return false;
        }

        $type = $this->request->get('type');
        if ($type === 'recording') {
            return $this->user()->can(Permission::SEE_LESSON_RECORDINGS);
        }

        return $this->user()->can(Permission::SEE_LESSON_MATERIALS); // There are now only 2 types. Fallback
    }

    public function rules(): array
    {
        return [

            'type' => [
                'string',
                'required',
                Rule::in(Material::allowedTypes())
            ],
            'lessons' => [
                'array'
            ],
            'lessons.*' => [
                'required',
                'integer',
                'min:0'
            ],
            'tags' => [
                'array'
            ],
            'tags.*' => [
                'required',
                'string',
            ],
            'orderBy' => [
                'string',
                Rule::in(['name', 'created_at', 'updated_at'])
            ],
            'order' => [
                Rule::in([1, -1])
            ],
            'limit' => [
                'integer',
                'min:0'
            ],
            'offset' => [
                'integer',
                'min:0'
            ],
            'content' => [
                'string'
            ]
        ];
    }

    public function queryParameters()
    {
        return [
            'type' => [
                'description' => 'Filter by type. Is required because different types have different permissions',
                'example' => 'material'
            ],
            'tags' => [
                'description' => 'Filter by tags',
                'example' => ['Fire', 'Law']
            ],
            'lessons' => [
                'description' => 'Filter by lesson id, only for authorized lessons',
                'example' => [332, 123]
            ],
            'orderBy' => [
                'description' => 'Property to order by [name, created_at, updated_at]',
                'example' => 'created_at'
            ],
            'order' => [
                'description' => 'Order 1 ASC -1 DESC',
                'example' => -1,
            ],
            'limit' => [
                'description' => 'Limit of records returned (Pagination)',
                'example' => 10,
            ],
            'offset' => [
                'description' => 'Offset of records to be skipped (page*limit) (Pagination)',
                'example' => 0,
            ],
            'content' => [
                'description' => 'Search by substring match for [name]',
                'example' => '',
            ],
        ];
    }
}