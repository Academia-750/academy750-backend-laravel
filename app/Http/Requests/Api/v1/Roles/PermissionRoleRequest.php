<?php

namespace App\Http\Requests\Api\v1\Roles;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permission_id' => [
                'required',
                'uuid',
            ],
        ];
    }
    public function bodyParameters()
    {
        return [
            'permission_id' => [
                'description' => 'UUID of an existing permission id',
                'example' => 'uuid',
            ]
        ];
    }
}