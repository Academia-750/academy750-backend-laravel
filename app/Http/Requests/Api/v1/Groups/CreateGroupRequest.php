<?php

namespace App\Http\Requests\Api\v1\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'min:6'
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:25',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ _-]+$/'
            ],
            'color' => [
                'required',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'

            ],
        ];
    }
}