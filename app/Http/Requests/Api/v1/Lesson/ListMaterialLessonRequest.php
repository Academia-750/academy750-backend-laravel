<?php

namespace App\Http\Requests\Api\v1\Lesson;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListMaterialLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'string',
                Rule::in(Material::allowedTypes())
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
                'description' => 'Filter by type',
                'example' => 'material'
            ],
            'tags' => [
                'description' => 'Filter by tags',
                'example' => ['Fire', 'Law']
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