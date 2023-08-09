<?php

namespace App\Http\Requests\Api\v1\Materials;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace' => [
                'numeric',
            ],
            'type' => [
                'string',
                Rule::in(Material::allowedTypes())
            ],
            'tags' => [
                'array'
            ],
            'tags.*' => [
                'string',
            ],
            'orderBy' => [
                'string',
                Rule::in(['name', 'created_at', 'updated_at', 'type'])
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