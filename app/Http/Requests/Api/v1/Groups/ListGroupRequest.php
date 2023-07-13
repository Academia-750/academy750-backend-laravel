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
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ _-]+$/'
            ],
            'names' => [
                'array'
            ],
            'names.*' => [
                'string',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ _-]+$/'
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
                Rule::in(['code', 'name', 'created_at'])
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
}