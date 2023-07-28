<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListWorkspaceRequest extends FormRequest
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
                Rule::in(['name', 'created_at', 'updated_at', 'materials_count'])
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
}