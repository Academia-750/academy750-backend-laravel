<?php

namespace App\Imports\Api\Services;

use App\Rules\Api\v1\SubtopicBelongsTopicRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionsImportValidation
{
    public static function validateRowValidator(array $row, $topicsArray): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($row, [
            'tema_uuid' => ['required', 'uuid', 'exists:topics,id'],
            'subtema_uuid' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "subtema_uuid"),
                ['uuid', 'exists:subtopics,id', new SubtopicBelongsTopicRule($row["tema_uuid"], $topicsArray)]
            )],
            'pregunta' => ['required','max:255'],
            'es_test' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "es_test"),
                ['in:si,no']
            )],
            'es_tarjeta_de_memoria' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "es_tarjeta_de_memoria"),
                ['in:si,no']
            )],
            "respuesta_correcta" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_correcta' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "es_agrupadora_respuesta_correcta"),
                ['in:si,no']
            )],
            "respuesta_1" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_1' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "es_agrupadora_respuesta_1"),
                ['in:si,no']
            )],
            "respuesta_2" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_2' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "es_agrupadora_respuesta_2"),
                ['in:si,no']
            )],
            "respuesta_3" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_3' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "es_agrupadora_respuesta_3"),
                ['in:si,no']
            )],
            'explicacion_texto' => ['required', 'max:400']
        ]);
    }

    public static function IssetRowInDataRows ($row, $key): bool
    {
        return array_key_exists($key, $row) && isset($row[$key]);
    }
}
