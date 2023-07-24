<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $procedure1 = "DROP PROCEDURE IF EXISTS `get_questions_by_card_memory`;
        CREATE PROCEDURE `get_questions_by_card_memory`(
            IN `id_usuario` VARCHAR(255),
            IN `id_test` VARCHAR(255),
            IN `tipo_cuestionario` VARCHAR(255),
            IN `numero_preguntas_solicitadas` INT
        )
        BEGIN
                DECLARE c INTEGER;

                SET c := (SELECT COUNT(*) from questions p WHERE p.questionable_id IN
                (SELECT testable_id FROM testables s, tests t WHERE t.id=s.test_id
                AND t.id=id_test)
                AND p.id NOT IN(SELECT m.question_id from question_test m, tests t
                WHERE t.id=m.test_id AND t.user_id=id_usuario AND t.test_type=tipo_cuestionario
                AND m.have_been_show_card_memory='yes')
                AND p.its_for_card_memory='yes');

                IF c < 1 THEN

                update question_test m SET m.have_been_show_card_memory='no' WHERE m.question_id IN
                (SELECT p.id from questions p WHERE p.questionable_id IN
                (SELECT testable_id FROM testables s, tests t
                WHERE t.id=s.test_id AND t.id=id_test AND t.user_id=id_usuario));

                END IF;

                SELECT p.id from questions p WHERE p.questionable_id IN
                 (SELECT testable_id FROM testables s, tests t WHERE t.id=s.test_id
                  AND t.user_id=id_usuario AND t.id=id_test) AND p.id NOT IN
                  (SELECT m.question_id from question_test m, tests t
                  WHERE t.id=m.test_id AND t.test_type=tipo_cuestionario AND
                  t.user_id=id_usuario and m.have_been_show_card_memory='yes')
                  ORDER BY RAND() LIMIT numero_preguntas_solicitadas;

        END";

        $procedure2 = "DROP PROCEDURE IF EXISTS `get_questions_by_test`;
        CREATE PROCEDURE `get_questions_by_test`(
            IN `id_usuario` VARCHAR(255),
            IN `id_test` VARCHAR(255),
            IN `tipo_cuestionario` VARCHAR(255),
            IN `numero_preguntas_solicitadas` INT
        )
        BEGIN
                DECLARE c INTEGER;

                SET c := (   SELECT Count(*) from questions p WHERE p.questionable_id IN
                (SELECT testable_id FROM testables s, tests t WHERE t.id=s.test_id
                 AND t.user_id=id_usuario AND t.id=id_test) AND p.id NOT IN
                 (SELECT m.question_id from question_test m, tests t
                 WHERE t.id=m.test_id AND t.test_type='test' AND
                 t.user_id=id_usuario and m.have_been_show_test='yes')
                 ORDER BY RAND() LIMIT numero_preguntas_solicitadas);

                IF c < 1 THEN

                update question_test m SET m.have_been_show_test='no' WHERE m.question_id IN
                (SELECT p.id from questions p WHERE p.questionable_id IN
                (SELECT testable_id FROM testables s, tests t
                WHERE t.id=s.test_id AND t.id=id_test AND t.user_id=id_usuario));

                END IF;



                SELECT p.id from questions p WHERE p.questionable_id IN
                 (SELECT testable_id FROM testables s, tests t WHERE t.id=s.test_id
                  AND t.user_id=id_usuario AND t.id=id_test) AND p.id NOT IN
                  (SELECT m.question_id from question_test m, tests t
                  WHERE t.id=m.test_id AND t.test_type=tipo_cuestionario AND
                  t.user_id=id_usuario and m.have_been_show_test='yes')
                  ORDER BY RAND() LIMIT numero_preguntas_solicitadas;

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
        if (app()->environment() === 'testing') {
            return;
        }

        $procedure1 = "DROP PROCEDURE IF EXISTS `get_questions_by_card_memory`";
        $procedure2 = "DROP PROCEDURE IF EXISTS `get_questions_by_test`";

        DB::unprepared($procedure1);
        DB::unprepared($procedure2);

    }
};
