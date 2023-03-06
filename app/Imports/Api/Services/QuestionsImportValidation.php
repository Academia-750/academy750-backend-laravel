<?php

namespace App\Imports\Api\Services;

use App\Rules\Api\v1\Questions\IsRequiredAnyTypeTestQuestionRule;
use App\Rules\Api\v1\Questions\IsThereShouldBeNoMoreThan1GroupingAnswer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionsImportValidation
{
    public static function validateRowValidator(array $row): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        // \Log::debug("Comienza a validar la fila");

        $isTypeCardMemory = self::IssetRowInDataRows($row, "es_tarjeta_de_memoria") && strtolower(trim($row['es_tarjeta_de_memoria'])) === 'si';
        $isTypeTest = self::IssetRowInDataRows($row, "es_test") && strtolower(trim($row['es_test'])) === 'si';
        $reasonText = self::IssetRowInDataRows($row, "explicacion_texto");
        $answerCorrect = (bool) self::IssetRowInDataRows($row, "respuesta_correcta");
        $answerOne = (bool) self::IssetRowInDataRows($row, "respuesta_1");
        $answerTwo = (bool) self::IssetRowInDataRows($row, "respuesta_2");
        $answerThree = (bool) self::IssetRowInDataRows($row, "respuesta_3");
        $isQuestionBinary = ((bool) $answerCorrect && (bool) $answerOne) && (!$answerTwo && !$answerThree) && $isTypeTest;

        if (self::IssetRowInDataRows($row, "es_para_subtema") && $row['es_para_subtema'] === 'si') {
            $optionsRulesConditionalItsForSubtopicOrTopic = [
                'required','uuid', 'exists:subtopics,id'
            ];
        } else {
            $optionsRulesConditionalItsForSubtopicOrTopic = [
                'required','uuid', 'exists:topics,id'
            ];
        }


        // \Log::debug("Comprobó algunos campos de la fila");
        return Validator::make($row, [
            /*'es_pregunta_binaria' => [
                Rule::when( (bool) $isTypeTest,
                    ['required','in:si,no']
                )
            ],*/
            'tema_uuid' => $optionsRulesConditionalItsForSubtopicOrTopic,
            /*'subtema_uuid' => ['nullable', Rule::when( (bool) self::IssetRowInDataRows($row, "subtema_uuid"),
                ['uuid', 'exists:subtopics,id']
            )],*/
            'es_para_subtema' => ['required', 'in:si,no'],
            'pregunta' => ['required','max:65535',
                new IsThereShouldBeNoMoreThan1GroupingAnswer(
                    $isQuestionBinary,
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_correcta') && strtolower(trim($row['es_agrupadora_respuesta_correcta'])) === 'si',
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_1') && strtolower(trim($row['es_agrupadora_respuesta_1'])) === 'si',
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_2') && strtolower(trim($row['es_agrupadora_respuesta_2'])) === 'si',
                    self::IssetRowInDataRows($row, 'es_agrupadora_respuesta_3') && strtolower(trim($row['es_agrupadora_respuesta_3'])) === 'si',
                ),
                new IsRequiredAnyTypeTestQuestionRule($isTypeTest, $isTypeCardMemory)
            ],
            'mostrar_explicacion_en_test' => [
                Rule::when((bool) $reasonText, [
                    'required','in:si,no'
                ])
            ],
            'mostrar_explicacion_en_tarjeta_de_memoria' => [
                Rule::when((bool) $reasonText, [
                    'required','in:si,no'
                ])
            ],
            'es_test' => ['required','in:si,no'],
            'es_tarjeta_de_memoria' => ['required','in:si,no'],
            "respuesta_correcta" => [
                'required', 'max:65535'
            ],
            'es_agrupadora_respuesta_correcta' => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                    ['required','in:si,no']
                )
            ],
            "respuesta_1" => [
                Rule::when( (bool) $isTypeTest,
                    ['required', 'max:65535']
                )
            ],
            'es_agrupadora_respuesta_1' => [
                Rule::when((bool) $isTypeTest && (bool) !$isQuestionBinary,
                ['required','in:si,no']
            )],
            "respuesta_2" => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                    ['required', 'max:65535']
                )
            ],
            'es_agrupadora_respuesta_2' => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                ['required','in:si,no']
            )],
            "respuesta_3" => [
                Rule::when((bool) $isTypeTest && (bool) !$isQuestionBinary, ['required', 'max:65535'])
            ],
            'es_agrupadora_respuesta_3' => [
                Rule::when( (bool) $isTypeTest && (bool) !$isQuestionBinary,
                ['required','in:si,no']
            )],
            'explicacion_texto' => ['nullable',
                Rule::when(
                    (bool) $reasonText || (bool) $isTypeCardMemory, [
                    'required', 'max:65535'
                ])]
        ]);
    }

    public static function IssetRowInDataRows (array $row, $key): bool
    {
        return array_key_exists($key, $row) && isset($row[$key]);
    }
}
