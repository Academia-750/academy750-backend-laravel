<?php

namespace App\Http\Requests\Api\v1\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ImportTopicsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['filesTopics.*' => "string[]"])] public function rules(): array
    {
        return [
            'filesTopics.*' => [
                'required',
                'mimes:csv,txt'
            ]
        ];
    }

    #[ArrayShape(['filesTopics' => "string"])] public function attributes(): array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'filesTopics' => 'Archivos CSV de los temas a importar',
        ];
    }
}
