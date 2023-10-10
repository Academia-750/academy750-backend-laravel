<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkspaceSearchRequest extends FormRequest
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
                'min:0',
                'max:20'
            ],
            'content' => [
                'string'
            ]
        ];
    }

    public function queryParameters()
    {
        return [

            'limit' => [
                'description' => 'Limit of records returned (Pagination) Default is 5',
                'example' => 5,
            ],
            'content' => [
                'description' => 'Search by substring match (name)',
                'example' => '',
            ],
        ];
    }
}