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

    public static function registerQuestion ($model, $dataQuestion) {
        return $model?->questions()->create([
            'question' =>  $dataQuestion["question"],
            'reason' => $dataQuestion["reason"],
            'is_visible' => 'yes',
            "its_for_test" => self::getEnumConditionalModel($dataQuestion["es_test"]),
            "its_for_card_memory" => self::getEnumConditionalModel($dataQuestion["es_tarjeta_de_memoria"]),
        ]);
    }

    public static function registerAnswersOfQuestion ($question_id, $dataAnswers): void
    {
        Answer::query()->create([
            'answer' => $dataAnswers["answer-correct"],
            'is_grouper_answer' => self::getEnumConditionalModel($dataAnswers["is-grouper-answer-correct"]),
            'is_correct_answer' => 'yes',
            'question_id' => $question_id
        ]);

        Answer::query()->create([
            'answer' => $dataAnswers["answer-1"],
            'is_grouper_answer' => self::getEnumConditionalModel($dataAnswers["is-grouper-answer-one"]),
            'is_correct_answer' => 'no',
            'question_id' => $question_id
        ]);

        Answer::query()->create([
            'answer' => $dataAnswers["answer-2"],
            'is_grouper_answer' => self::getEnumConditionalModel($dataAnswers["is-grouper-answer-two"]),
            'is_correct_answer' => 'no',
            'question_id' => $question_id
        ]);

        Answer::query()->create([
            'answer' => $dataAnswers["answer-3"],
            'is_grouper_answer' => self::getEnumConditionalModel($dataAnswers["is-grouper-answer-three"]),
            'is_correct_answer' => 'no',
            'question_id' => $question_id
        ]);
    }

    public static function getDataFormattedForRegisterQuestions ($row): array {
        return [
            "question" => $row["pregunta"],
            "reason" => $row["explicacion_texto"],
            "topic_id" => $row["tema_uuid"],
            "subtopic_id" => (bool) QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "subtema_uuid")
                && $row["subtema_uuid"],
            "es_test" => QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "es_test")
                && $row["es_test"],
            "es_tarjeta_de_memoria" => QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "es_tarjeta_de_memoria")
                && $row["es_tarjeta_de_memoria"],
        ];
    }

    public static function getDataFormattedForRegisterAnswersOfQuestion ($row): array {
        return [
            "answer-correct" => $row["respuesta_correcta"],
            "is-grouper-answer-correct" =>
                QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "es_agrupadora_respuesta_correcta")
                && $row["es_agrupadora_respuesta_correcta"],
            "answer-1" => $row["respuesta_1"],
            "is-grouper-answer-one" =>
                QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "es_agrupadora_respuesta_1")
                && $row["es_agrupadora_respuesta_1"],
            "answer-2" => $row["respuesta_2"],
            "is-grouper-answer-two" =>
                QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "es_agrupadora_respuesta_2")
                && $row["es_agrupadora_respuesta_2"],
            "answer-3" => $row["respuesta_3"],
            "is-grouper-answer-three" =>
                QuestionsImportValidation::IssetRowInDataRows($row->toArray(), "es_agrupadora_respuesta_3")
                && $row["es_agrupadora_respuesta_3"],
        ];
    }
}
