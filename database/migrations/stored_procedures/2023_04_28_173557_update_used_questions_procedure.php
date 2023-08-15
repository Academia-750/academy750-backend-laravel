<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public string $nameProcedure;

    public function __construct()
    {
        $this->nameProcedure = 'update_used_questions_procedure';
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN `id_test` INT
        )
        BEGIN
            DECLARE id_usuario VARCHAR(36);
            DECLARE id_oposicion VARCHAR(36);
            DECLARE index_loop INTEGER;
            DECLARE v_done INTEGER DEFAULT FALSE;
            DECLARE v_id VARCHAR(36);
            DECLARE cur1 CURSOR FOR SELECT question_id FROM tmp_questions;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;


            DROP TEMPORARY TABLE IF EXISTS tmp_questions;
            CREATE TEMPORARY TABLE tmp_questions (
                question_id VARCHAR(36),
                topic_id VARCHAR(36),
                subtopic_id VARCHAR(36),
                user_id VARCHAR(36),
                opposition_id VARCHAR(36)
            );

        DROP TEMPORARY TABLE IF EXISTS tmp_log;
            CREATE TEMPORARY TABLE tmp_log(
                ltext VARCHAR(100),
                question_id VARCHAR(36)
            );

            INSERT INTO tmp_questions
                select distinct qt.question_id, top.id as topic_id, '' as subtopic_id, t.user_id, t.opposition_id
                from tests t
                INNER JOIN question_test qt on qt.test_id = t.id
                INNER JOIN questions q on q.id = qt.question_id
                INNER JOIN topics top on top.id = q.questionable_id
                where t.id = id_test
                and q.questionable_type = 'App\\\\Models\\\\Topic'
                and qt.status_solved_question <> 'correct'
              UNION
                select distinct qt.question_id, top.id as topic_id, st.id as subtopic_id, t.user_id, t.opposition_id
                from tests t
                INNER JOIN question_test qt on qt.test_id = t.id
                INNER JOIN questions q on q.id = qt.question_id
                INNER JOIN subtopics st on st.id = q.questionable_id
                INNER JOIN topics top on top.id = st.topic_id
                where t.id = id_test
                and q.questionable_type = 'App\\Models\\Subtopic'
                and qt.status_solved_question <> 'correct';

            SET index_loop:=0;
            SET id_usuario:=(SELECT user_id FROM tmp_questions LIMIT 1);
            SET id_oposicion:=(SELECT opposition_id FROM tmp_questions LIMIT 1);

        OPEN cur1;

            read_loop: LOOP
                FETCH cur1 INTO v_id;
                IF v_done THEN
                  LEAVE read_loop;
                END IF;

                IF EXISTS(SELECT 1 FROM questions_used_test WHERE question_id =v_id and user_id = id_usuario and opposition_id = id_oposicion LIMIT 1) THEN
                  UPDATE questions_used_test SET result = 0 WHERE question_id =v_id and user_id = id_usuario and opposition_id = id_oposicion;
        /*INSERT INTO tmp_log VALUES ('UPDATE - QUESTION',v_id);
        INSERT INTO tmp_log VALUES ('UPDATE - USUARIO',id_usuario );
        INSERT INTO tmp_log VALUES ('UPDATE - OPO',id_oposicion );*/
                ELSE
                  INSERT INTO questions_used_test
                  SELECT topic_id, subtopic_id, user_id, opposition_id, question_id, 0
                  FROM tmp_questions
                  WHERE question_id = v_id;
        /*INSERT INTO tmp_log VALUES ('INSERT - QUESTION',v_id);
        INSERT INTO tmp_log VALUES ('INSERT - USUARIO',id_usuario );
        INSERT INTO tmp_log VALUES ('INSERT - OPO',id_oposicion );*/
                END IF;

            END LOOP;

            CLOSE cur1;

          DELETE FROM tmp_questions;

            INSERT INTO tmp_questions
                select distinct qt.question_id, top.id as topic_id, '' as subtopic_id, t.user_id, t.opposition_id
                from tests t
                INNER JOIN question_test qt on qt.test_id = t.id
                INNER JOIN questions q on q.id = qt.question_id
                INNER JOIN topics top on top.id = q.questionable_id
                where t.id = id_test
                and q.questionable_type = 'App\\\\Models\\\\Topic'
                and qt.status_solved_question = 'correct'
              UNION
                select distinct qt.question_id, top.id as topic_id, st.id as subtopic_id, t.user_id, t.opposition_id
                from tests t
                INNER JOIN question_test qt on qt.test_id = t.id
                INNER JOIN questions q on q.id = qt.question_id
                INNER JOIN subtopics st on st.id = q.questionable_id
                INNER JOIN topics top on top.id = st.topic_id
                where t.id = id_test
                and q.questionable_type = 'App\\Models\\Subtopic'
                and qt.status_solved_question = 'correct';

            SET index_loop:=0;
            SET v_done:=FALSE;

            OPEN cur1;

            read_loop: LOOP
                FETCH cur1 INTO v_id;
                IF v_done THEN
                  LEAVE read_loop;
                END IF;

                IF EXISTS(SELECT 1 FROM questions_used_test WHERE question_id =v_id and user_id = id_usuario and opposition_id = id_oposicion LIMIT 1) THEN
                  UPDATE questions_used_test SET result = 1 WHERE question_id =v_id and user_id = id_usuario and opposition_id = id_oposicion;
        /*INSERT INTO tmp_log VALUES ('UPDATE - OK - QUESTION',v_id);
        INSERT INTO tmp_log VALUES ('UPDATE - OK - USUARIO',id_usuario );
        INSERT INTO tmp_log VALUES ('UPDATE - OK - OPO',id_oposicion );*/
                ELSE
                  INSERT INTO questions_used_test
                  SELECT topic_id, subtopic_id, user_id, opposition_id, question_id, 1
                  FROM tmp_questions
                  WHERE question_id = v_id;
        /*INSERT INTO tmp_log VALUES ('INSERT - OK - QUESTION',v_id);
        INSERT INTO tmp_log VALUES ('INSERT - OK - USUARIO',id_usuario );
        INSERT INTO tmp_log VALUES ('INSERT - OK - OPO',id_oposicion );*/
                END IF;

            END LOOP;

            CLOSE cur1;

            DROP TEMPORARY TABLE tmp_questions;
            DROP TEMPORARY TABLE tmp_log;
        END";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";

        DB::unprepared($procedure);
    }
};