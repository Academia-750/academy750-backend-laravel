<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => [
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