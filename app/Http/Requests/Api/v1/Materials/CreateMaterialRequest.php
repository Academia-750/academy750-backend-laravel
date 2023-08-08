<?php

namespace App\Http\Requests\Api\v1\Materials;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMaterialRequest extends FormRequest
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
            'type' => [
                'required',
                'string',
                Rule::in(Material::allowedTypes())
            ],
        ];
    }
}