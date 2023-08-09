<?php

namespace App\Http\Requests\Api\v1\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JoinGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'uuid',
            ],
        ];
    }

    public function bodyParameters()
    {
        return [
            'user_id' => [
                'description' => 'Users ID',
            ],
        ];
    }
}