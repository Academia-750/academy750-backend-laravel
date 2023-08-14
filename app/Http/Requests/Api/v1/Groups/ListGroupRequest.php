<?php

namespace App\Http\Requests\Api\v1\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codes' => [
                'array'
            ],
            'codes.*' => [
                'string',
                config('constants.string_request_regex')
            ],
            'names' => [
                'array'
            ],
            'names.*' => [
                'string',
                config('constants.string_request_regex')
            ],
            'colors' => [
                'array'
            ],
            'colors.*' => [
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'orderBy' => [
                'string',
                Rule::in(['code', 'name', 'created_at', 'active_users', 'updated_at'])
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
                'string',
            ]

        ];
    }

    public function queryParameters()
    {
        return [
            'colors' => [
                'description' => 'List of colors to search in',
            ],
            'codes' => [
                'description' => 'List of codes to search in',
            ],
            'tags' => [
                'description' => 'List of tags to search in',
            ],
            'orderBy' => [
                'description' => 'Property to order by',
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
                'description' => 'Search by substring match (name, code)',
                'example' => '',
            ],
        ];
    }
}