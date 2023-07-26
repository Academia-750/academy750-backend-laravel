<?php

namespace App\Http\Requests\Api\v1\Users;

use Illuminate\Foundation\Http\FormRequest;


class SearchUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => [
                'alpha_num',
            ],

        ];
    }
}