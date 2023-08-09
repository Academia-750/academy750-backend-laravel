<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;

class CreateWorkspaceRequest extends FormRequest
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
                config('constants.string_request_regex')
            ],
        ];
    }

    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Workspace Name',
                'example' => "General"
            ],
        ];
    }
}