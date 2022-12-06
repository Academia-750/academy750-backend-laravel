<?php

namespace App\Http\Requests\Api\v1\Subtopics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class ImportSubtopicsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['filesSubtopics.*' => "string[]"])] public function rules(): array
    {
        return [
            'filesSubtopics.*' => [
                'required',
                'mimes:csv'
            ]
        ];
    }

    #[ArrayShape(['filesSubtopics' => "string"])] public function attributes(): array
    {
        // Este metodo remplaza cada índice que es mostrado en el error
        return [
            'filesSubtopics' => 'Archivos CSV de los subtemas a importar',
        ];
    }
}
