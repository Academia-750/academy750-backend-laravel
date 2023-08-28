<?php

namespace App\Http\Requests\Api\v1\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'min:3',
                'max:25',
                config('constants.string_request_regex')
            ],
            'default_role' => [
                'boolean',
                Rule::in([true])
            ],
        ];
    }
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Role Name. Not allowed special characters',
                'example' => 'Only Lessons',
            ],
            'permissions' => [
                'description' => 'List of UUIDs that are the permissions associated to this role',
                'example' => 'Only Lessons',
            ],
            'default_role' => [
                'description' => 'Switch the default role to this role. You can not disable it (pass false) but you can enable another role. There is always 1 and only 1 default role',
                'example' => 'true',
            ],
        ];
    }
}