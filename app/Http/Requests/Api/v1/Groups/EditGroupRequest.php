<?php

namespace App\Http\Requests\Api\v1\Groups;

use Illuminate\Foundation\Http\FormRequest;

class EditGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'string',
                'min:6'
            ],
            'name' => [
                'string',
                'min:3',
                'max:25',
                config('constants.string_request_regex')
            ],
            'color' => [
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
        ];
    }

    public function bodyParameters()
    {
        return [
            'code' => [
                'description' => 'Group Code. Must be unique.',
                'example' => "AUGTDO"
            ],
            'name' => [
                'description' => 'Group Name',
                'example' => 'Advanced 3',
            ],
            'color' => [
                'description' => 'Groups Color, a color can be used only in one group',
                'example' => '#FF0000',
            ]
        ];
    }
}