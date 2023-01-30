<?php
namespace Database\Seeders\trait;

trait QuestionsHelpersTrait
{
    public function registerQuestionsModel ($model, $fakerText, $its_for_test, $its_for_card_memory, $countTotalQuestions, $tipoTestPreguntaTexto): void {
        foreach ( range(1, $countTotalQuestions) as $number ) {
            $question = $model->questions()->create([
                'question' => "Pregunta {$number} - {$tipoTestPreguntaTexto}",
                'reason' => "Explicacion {$number} - {$fakerText}",
                'is_visible' => "yes",
                'its_for_test' => $its_for_test,
                'its_for_card_memory' => $its_for_card_memory,
            ]);

            $question->refresh();

            $this->registerAnswersOfQuestion($question);
        }



    }

    public function registerAnswersOfQuestion ($question): void
    {

            $thereIsAnswerGrouper = false;
            $thereIsAnswerCorrect = false;

            $answers = [
                [
                    'answer' => "Answer - 1 (Correct) Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'yes',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'answer' => "Answer - 2 (Agrupadora) Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'yes',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'answer' => "Answer - 3 Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'answer' => "Answer - 4 Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ]
            ];

            shuffle($answers);


            foreach ($answers as $answer) {
                $question->answers()->create([
                    'answer' => $answer['answer'],
                    'is_grouper_answer' => $answer['is_grouper_answer'],
                    'is_correct_answer' => $answer['is_correct_answer']
                ]);
            }
    }
}
