<?php

namespace App\Http\Requests\Api\v1\Roles;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:25',
                config('constants.string_request_regex')
            ],
        ];
    }
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Role Name',
                'example' => 'Teachers',
            ],
        ];
    }
}