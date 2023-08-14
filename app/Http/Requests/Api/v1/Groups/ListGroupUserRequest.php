<?php

namespace App\Http\Requests\Api\v1\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListGroupUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discharged' => [
                'boolean'
            ],
            'orderBy' => [
                'string',
                Rule::in(['dni', 'full_name', 'created_at', 'discharged_at', 'updated_at', 'phone', 'email'])
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
            'discharged' => [
                'description' => 'If set to FALSE (or by default) will return active students. If set to true will return old students (already discharged) ',
                'example' => false,
                'default' => false
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