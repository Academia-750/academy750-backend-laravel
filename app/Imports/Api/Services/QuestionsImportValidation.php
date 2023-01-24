<?php

namespace App\Imports\Api\Services;

use App\Rules\Api\v1\Question\IsRequiredAnyReasonTextOrImageQuestionRule;
use App\Rules\Api\v1\Questions\IsRequiredAnyTypeTestQuestionRule;
use App\Rules\Api\v1\Questions\IsRequiredTypeTestOfQuestion;
use App\Rules\Api\v1\Questions\IsThereShouldBeNoMoreThan1GroupingAnswer;
use App\Rules\Api\v1\SubtopicBelongsTopicRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionsImportValidation
{
    public static function validateRowValidator(array $row, $topicsArray): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        $isTypeCardMemory = self::IssetRowInDataRows($row, "es_tarjeta_de_memoria") && strtolower(trim($row['es_tarjeta_de_memoria'])) === 'si';
        $isTypeTest = self::IssetRowInDataRows($row, "es_test") && strtolower(trim($row['es_test'])) === 'si';
        $reasonText = self::IssetRowInDataRows($row, "explicacion_texto");
        $answerCorrect = (bool) self::IssetRowInDataRows($row, "respuesta_correcta");
        $answerOne = (bool) self::IssetRowInDataRows($row, "respuesta_1");
        $answerTwo = (bool) self::IssetRowInDataRows($row, "respuesta_2");
        $answerThree = (bool) self::IssetRowInDataRows($row, "respuesta_3");
        $isQuestionBinary = ((bool) $answerCorrect && (bool) $answerOne) && (!$answerTwo && !$answerThree) && $isTypeTest;

        return Validator::make($row, [
            /*'es_pregunta_binaria' => [
                Rule::when( (bool) $isTypeTest,
                    ['required','in:si,no']
                )
            ],*/
            'tema_uuid' => ['required', 'uuid', 'exists:topics,id'],
            'subtema_uuid' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "subtema_uuid"),
                ['uuid', 'exists:subtopics,id', new SubtopicBelongsTopicRule($row["tema_uuid"], $topicsArray)]
            )],
            'pregunta' => ['required','max:255',
                new IsThereShouldBeNoMoreThan1GroupingAnswer(
                    $isQuestionBinary,
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_correcta') && strtolower(trim($row['es_agrupadora_respuesta_correcta'])) === 'si',
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_1') && strtolower(trim($row['es_agrupadora_respuesta_1'])) === 'si',
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_2') && strtolower(trim($row['es_agrupadora_respuesta_2'])) === 'si',
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_3') && strtolower(trim($row['es_agrupadora_respuesta_3'])) === 'si',
                ),
                new IsRequiredAnyTypeTestQuestionRule($isTypeTest, $isTypeCardMemory)
            ],
            'mostrar_explicacion_en_test' => ['required','in:si,no'],
            'mostrar_explicacion_en_tarjeta_de_memoria' => ['required','in:si,no'],
            'es_test' => ['required','in:si,no'],
            'es_tarjeta_de_memoria' => ['required','in:si,no'],
            "respuesta_correcta" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_correcta' => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                    ['required','in:si,no']
                )
            ],
            "respuesta_1" => [
                Rule::when( (bool) $isTypeTest,
                    ['required', 'max:255']
                )
            ],
            'es_agrupadora_respuesta_1' => [
                Rule::when((bool) $isTypeTest && (bool) !$isQuestionBinary,
                ['required','in:si,no']
            )],
            "respuesta_2" => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                    ['required', 'max:255']
                )
            ],
            'es_agrupadora_respuesta_2' => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                ['required','in:si,no']
            )],
            "respuesta_3" => [
                Rule::when((bool) $isTypeTest && (bool) !$isQuestionBinary, ['required', 'max:255'])
            ],
            'es_agrupadora_respuesta_3' => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                ['required','in:si,no']
            )],
            'explicacion_texto' => ['nullable',
                Rule::when(
                    (bool) $reasonText || (bool) $isTypeCardMemory, [
                    'required', 'max:400'
                ])]
        ]);
    }

    public static function IssetRowInDataRows (array $row, $key): bool
    {
        return array_key_exists($key, $row) && isset($row[$key]);
    }
}
