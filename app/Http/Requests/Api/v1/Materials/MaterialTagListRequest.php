<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialTagListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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

            'orderBy' => [
                'description' => 'Property to order by',
                'example' => 'created_at'
            ],
            'order' => [
                'description' => 'Order 1 ASC -1 DESC',
                'example' => -1,
            ],
            'offset' => [
                'description' => 'Offset of records to be skipped (page*limit) (Pagination)',
                'example' => 0,
            ],
            'limit' => [
                'description' => 'Limit of records returned (Pagination)',
                'example' => 10,
            ],
            'content' => [
                'description' => 'Search by substring match (tag name)',
                'example' => '',
            ],
        ];
    }
}