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
            'user_id' => [
                'uuid',
                'required',
            ],
            'role_id' => [
                'uuid',
                'required',
            ]
        ];
    }

    public function bodyParameters()
    {
        return [
            'user_id' => [
                'description' => 'The user uuid that will be updated',
                'example' => 'uuid',
            ],
            'role_id' => [
                'description' => 'The role id that the user will have',
                'example' => 'uuid',
            ],
        ];
    }
}