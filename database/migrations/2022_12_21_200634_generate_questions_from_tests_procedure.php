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
        $procedure1= "DROP PROCEDURE IF EXISTS `get_questions_by_card_memory`;
        CREATE PROCEDURE `get_questions_by_card_memory`(
            IN `id_usuario` INT,
            IN `id_test` INT,
            IN `id_tipo_cuestionario` INT,
            IN `numero_preguntas_solicitadas` INT
        )
        BEGIN
                DECLARE c INTEGER;

                SET c := (SELECT COUNT(*) from questions p WHERE p.questionable_id IN (SELECT testable_id FROM testables s, users u, tests t WHERE t.id=s.test_id AND u.id=t.user_id AND t.user_id=id_usuario AND t.id=id_test) AND q.id NOT IN(SELECT m.question_id from question_test m, users u, tests t WHERE u.id=t.user_id AND t.id=m.test_id AND t.test_type_id=id_tipo_cuestionario AND u.id=id_usuario AND q.have_been_show_card_memory='yes'));

                IF c < 1 THEN

                update question_test m SET q.have_been_show_card_memory='no' WHERE m.question_id IN (SELECT p.id from questions p WHERE p.questionable_id IN (SELECT testable_id FROM testables s, users u, tests t WHERE t.id=s.test_id AND u.id=t.user_id AND t.user_id=id_usuario AND t.id=id_test));

                END IF;

                SELECT p.id from questions p WHERE p.questionable_id IN (SELECT testable_id FROM testables s, users u, tests t WHERE t.id=s.test_id AND u.id=t.user_id AND t.user_id=id_usuario AND t.id=id_test) ORDER BY RAND LIMIT numero_preguntas_solicitadas ;

        END";

        $procedure2= "DROP PROCEDURE IF EXISTS `get_questions_by_test`;
        CREATE PROCEDURE `get_questions_by_test`(
            IN `id_usuario` INT,
            IN `id_test` INT,
            IN `id_tipo_cuestionario` INT,
            IN `numero_preguntas_solicitadas` INT
        )
        BEGIN
                DECLARE c INTEGER;

                SET c := (SELECT COUNT(*) from questions p WHERE p.questionable_id IN (SELECT testable_id FROM testables s, users u, tests t WHERE t.id=s.test_id AND u.id=t.user_id AND t.user_id=id_usuario AND t.id=id_test) AND q.id NOT IN(SELECT m.question_id from question_test m, users u, tests t WHERE u.id=t.user_id AND t.id=m.test_id AND t.test_type_id=id_tipo_cuestionario AND u.id=id_usuario AND q.have_been_show_test='yes'));

                IF c < 1 THEN

                update question_test m SET q.have_been_show_test='no' WHERE m.question_id IN (SELECT p.id from questions p WHERE p.questionable_id IN (SELECT testable_id FROM testables s, users u, tests t WHERE t.id=s.test_id AND u.id=t.user_id AND t.user_id=id_usuario AND t.id=id_test));

                END IF;

                SELECT p.id from questions p WHERE p.questionable_id IN (SELECT testable_id FROM testables s, users u, tests t WHERE t.id=s.test_id AND u.id=t.user_id AND t.user_id=id_usuario AND t.id=id_test) ORDER BY RAND LIMIT numero_preguntas_solicitadas ;

        END";

        DB::unprepared($procedure1);
        DB::unprepared($procedure2);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure1= "DROP PROCEDURE IF EXISTS `get_questions_by_card_memory`";
        $procedure2= "DROP PROCEDURE IF EXISTS `get_questions_by_test`";

        DB::unprepared($procedure1);
        DB::unprepared($procedure2);

    }
};
