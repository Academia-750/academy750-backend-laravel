<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure1= "DROP PROCEDURE IF EXISTS `get_questions_wrong_history_by_topic_and_tests_student_procedure`;
        CREATE PROCEDURE `get_questions_wrong_history_by_topic_and_tests_student_procedure`(
            IN `id_usuario` VARCHAR(255),
            IN `id_topic` VARCHAR(255)
        )
        BEGIN

        SELECT q.question_id AS 'question_wrong_id', t.id AS 'test_wrong_id', c.question AS 'texto_pregunta', a.answer AS 'texto_respuesta_marcada', rc.answer AS 'texto_respuesta_correcta', c.reason AS 'explicacion_pregunta', t.finished_at AS 'fecha_test'
        from question_test q, tests t, questions c, answers a, answers rc
        where t.id=q.test_id and t.user_id=id_usuario and q.answer_id=a.id and rc.question_id=q.question_id and rc.is_correct_answer='yes'
        and c.id=q.question_id
        and (c.questionable_id =id_topic or c.questionable_id IN
        (select id from subtopics s where s.topic_id=id_topic))
         and q.status_solved_question='wrong' and t.is_solved_test='yes' ORDER BY t.finished_at DESC;

        END";

        DB::unprepared($procedure1);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure1= "DROP PROCEDURE IF EXISTS `get_questions_wrong_history_by_topic_and_tests_student_procedure`";


        DB::unprepared($procedure1);


    }
};
