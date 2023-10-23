<?php

namespace App\Http\Requests\Api\v1\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class SearchUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => [
                'nullable',
                'string',
                config('constants.string_request_regex')
            ],
            'limit' => [
                'integer',
                'min:0'
            ],
        ];
    }

    public function queryParameters()
    {
        return [
            'content' => [
                'description' => 'Search by substring match (name, dni)',
                'example' => '',
            ],
            'limit' => [
                'description' => 'Limit of records returned',
                'example' => 10,
            ],
        ];
    }
}