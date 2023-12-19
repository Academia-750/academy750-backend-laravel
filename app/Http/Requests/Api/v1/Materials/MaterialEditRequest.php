<?php

namespace App\Http\Requests\Api\v1\Materials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialEditRequest extends FormRequest
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
            'watermark' => [
                'boolean'
            ],
        ];
    }

    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Material Name',
                'example' => "Fire Laws V1"
            ],
            'tags' => [
                'description' => 'Material Tags',
                'example' => ['Fire', 'Law']
            ],
            'url' => [
                'description' => 'Material URL',
                'example' => "https://my-cloud.com/file/123dade123d"
            ],
        ];
    }
}
