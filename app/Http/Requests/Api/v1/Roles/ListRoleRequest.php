<?php

namespace App\Http\Requests\Api\v1\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    private $orderBy = ['name', 'alias_name', 'users_count', 'created_at', 'updated_at'];
    public function rules(): array
    {
        return [
            'orderBy' => [
                'string',
                Rule::in($this->orderBy)
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
            'orderBy' => [
                'description' => 'Property to order by: ' . join($this->orderBy),
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
                'description' => 'Search by substring match (name)',
                'example' => '',
            ],
        ];
    }
}