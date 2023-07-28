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
}