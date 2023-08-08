<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditMaterialRequest extends FormRequest
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
                config('constants.string_request_regex')
            ],
            'tags' => [
                'array'
            ],
            'tags.*' => [
                'string',
                config('constants.string_request_regex')
            ],
            'url' => [
                'string',
                'url'
            ],
        ];
    }
}