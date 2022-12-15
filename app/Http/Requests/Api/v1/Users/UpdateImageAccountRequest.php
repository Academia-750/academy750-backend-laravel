<?php

namespace App\Http\Requests\Api\v1\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class UpdateImageAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['image' => "string[]"])]
    public function rules(): array
    {
        return [
            'image' => [
                'required', 'file', 'image', 'mimes:jpeg,jpg,png,gif', 'max:10000' // 10mb
            ]
        ];
    }

    #[ArrayShape(['image' => "string"])]
    public function attributes():array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'image' => 'Imagen'
        ];
    }
}
