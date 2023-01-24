<?php

namespace App\Imports\Api\Services;

use App\Models\Answer;

class QuestionsImportService
{
    public static function getEnumConditionalModel ($value): string {
        if ($value === 'si') {
            return 'yes';
        }
        return 'no';
    }

    public static function registerQuestion ($model, $dataQuestion, $isTest) {
        \Log::debug($dataQuestion['is-question-binary-alternatives']);
        \Log::debug((bool) $dataQuestion['is-question-binary-alternatives'] ? 'yes' : 'no');

        return $model?->questions()->create([
            'question' =>  trim($dataQuestion["question"]),
            'reason' => trim($dataQuestion["reason"]),
            'is_question_binary_alternatives' => !$isTest ? 'no' : ( (bool) $dataQuestion['is-question-binary-alternatives'] ? 'yes' : 'no'),
            "show_reason_text_in_test" => $dataQuestion['show_reason_text_in_test'],
            "show_reason_text_in_card_memory" => $dataQuestion['show_reason_text_in_card_memory'],
            'is_visible' => 'yes',
            "its_for_test" => self::getEnumConditionalModel($dataQuestion["es_test"]),
            "its_for_card_memory" => self::getEnumConditionalModel($dataQuestion["es_tarjeta_de_memoria"]),
        ]);
    }

    public static function registerAnswersOfQuestion ($dataAnswers): void
    {

        foreach ($dataAnswers as $answer) {
            Answer::query()->create([
                'answer' => trim($answer["answer"]),
                'is_grouper_answer' => $answer["is_grouper_answer"],
                'is_correct_answer' => $answer["is_correct_answer"],
                'question_id' => $answer["question_id"],
            ]);
        }
    }

    public static function getDataFormattedForRegisterQuestions (array $row): array {
        $isTypeTest = QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && strtolower(trim($row['es_test'])) === 'si';
        $answerCorrect = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_correcta");
        $answerOne = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_1");
        $answerTwo = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_2");
        $answerThree = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_3");

        return [
            "show_reason_text_in_test" => $row["mostrar_explicacion_en_test"],
            "show_reason_text_in_card_memory" => $row["mostrar_explicacion_en_tarjeta_de_memoria"],
            "question" => $row["pregunta"],
            "reason" => $row["explicacion_texto"],
            "topic_id" => $row["tema_uuid"],
            "is-question-binary-alternatives" => ((bool) $answerCorrect && (bool) $answerOne) && (!$answerTwo && !$answerThree) && $isTypeTest,
            "subtopic_id" => (bool) QuestionsImportValidation::IssetRowInDataRows($row, "subtema_uuid") ? $row["subtema_uuid"] : null,
            "es_test" => QuestionsImportValidation::IssetRowInDataRows($row, "es_test") ? $row["es_test"] : null,
            "es_tarjeta_de_memoria" => QuestionsImportValidation::IssetRowInDataRows($row, "es_tarjeta_de_memoria") ? $row["es_tarjeta_de_memoria"] : null,
        ];
    }

    public static function getDataFormattedForRegisterAnswersOfQuestion (array $row, string $question_id): array {

        $isTest = QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && strtolower(trim($row['es_test'])) === 'si';
        $answerCorrect = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_correcta");
        $answerOne = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_1");
        $answerTwo = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_2");
        $answerThree = (bool) QuestionsImportValidation::IssetRowInDataRows($row, "respuesta_3");
        $isTypeTest = QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && strtolower(trim($row['es_test'])) === 'si';

        $isQuestionBinary = ((bool) $answerCorrect && (bool) $answerOne) && (!$answerTwo && !$answerThree) && $isTypeTest;

        if (!$isTest) {
            $answers = [
                [
                    'answer' => trim($row['respuesta_correcta']),
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'yes',
                    'question_id' => $question_id,
                ]
            ];

            return $answers;
        }


        if ($isQuestionBinary && $isTest) {
            $answers = [
                [
                    'answer' => trim($row['respuesta_correcta']),
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'yes',
                    'question_id' => $question_id,
                ],
                [
                    'answer' => trim($row['respuesta_1']),
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'no',
                    'question_id' => $question_id,
                ]
            ];

            shuffle($answers);

            return $answers;
        }

        $answers = [
            [
                'answer' => trim($row['respuesta_correcta']),
                'is_grouper_answer' => strtolower(trim($row['es_agrupadora_respuesta_correcta'])) === 'si' ? 'yes' : 'no',
                'is_correct_answer' => 'yes',
                'question_id' => $question_id,
            ],
            [
                'answer' => trim($row['respuesta_1']),
                'is_grouper_answer' => strtolower(trim($row['es_agrupadora_respuesta_1'])) === 'si' ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question_id,
            ],
            [
                'answer' => trim($row['respuesta_2']),
                'is_grouper_answer' => strtolower(trim($row['es_agrupadora_respuesta_2'])) === 'si' ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question_id,
            ],
            [
                'answer' => trim($row['respuesta_3']),
                'is_grouper_answer' => strtolower(trim($row['es_agrupadora_respuesta_3'])) === 'si' ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question_id,
            ],
        ];

        shuffle($answers);

        return $answers;
    }
}
