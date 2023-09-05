<?php

namespace App\Http\Requests\Api\v1\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UserRoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => [
                'uuid',
                'required',
            ]
        ];
    }

    public function queryParameters()
    {
        return [
            'role_id' => [
                'description' => 'The role id that the user will have',
                'example' => '',
            ],
        ];
    }
}