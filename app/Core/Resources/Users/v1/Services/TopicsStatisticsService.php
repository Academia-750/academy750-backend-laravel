<?php

namespace App\Core\Resources\Users\v1\Services;

use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

class TopicsStatisticsService
{
    /**
     * Me devuelve los Temas que se han usado en los Tests que ha completado un Alumno
     *
     * @return array
     */
    public static function getTopicsByTestsCompleted (): array {
        $topicsIdData = [];

        $tests = self::getTestsCompletedByStudent();

        foreach ($tests as $test) {

            $topics = $test->topics()->cursor();

            foreach ( $topics as $topic) {
                $topicsIdData[] = $topic->getKey();
            }

        }

        return $topicsIdData;
    }

    /**
     * Este me consulta absolutamente todas las preguntas que pertenecen a cada uno de los temas que se han usado en los Tests del alumno
     *
     * @param $topics_id
     * @return array
     */
    public static function getQuestionsByTopics ($topics_id): array {
        $questions_id = [];

        foreach ($topics_id as $topic_id) {
            $topic = Topic::query()->findOrFail($topic_id);
            $questions = $topic->questions()->cursor();
            foreach ( $questions as $question ) {
                $questions_id[] = $question->getKey();
            }
        }

        return $questions_id;
    }

    /**
     * comparar preguntas fallidas de los Tests contra las preguntas de cada tema usado en los Tests del usuario
     *
     * @return array
     */
    public static function getQuestionsFailedBelongsToTopicAndTest (Topic $topic): array {
        $questions_failed_id = array_chunk( array_unique(self::getQuestionsFailedByTests()), 100 );
        $questions_id_by_topics_student = $topic->questions()->pluck('questions.id')->toArray();

        $questions_id = [];

        foreach ( $questions_failed_id as $question_failed_id ) {
            if (in_array($question_failed_id, $questions_id_by_topics_student, true)) {
                $questions_id[] = $question_failed_id;
            }
        }

        return $questions_id;
    }

    /**
     * Obtiene todas las preguntas fallidas de cada uno de los Tests que el usuario ha completado
     *
     * @return array
     */
    public static function getQuestionsFailedByTests (): array {
        $tests = self::getTestsCompletedByStudent();
        $questionsFailed_id = [];

        foreach ($tests as $test) {
            $questionsFailedCursor = $test->questions()->where('status_solved_question', '=', 'wrong')->cursor();

            foreach ( $questionsFailedCursor as $questionFailed ) {
                $questionsFailed_id[] = $questionFailed->getRouteKey();
            }
        }

        return $questionsFailed_id;
    }

    /**
     * Devuelve los Tests completados que pertenecen al usuario
     *
     *
     */
    public static function getTestsCompletedByStudent () {
        return Auth::user()
            ?->tests()
            ->where('test_type', '=', 'test')
            ->where('is_solved_test', '=', 'yes')
            ->cursor();
    }

}
