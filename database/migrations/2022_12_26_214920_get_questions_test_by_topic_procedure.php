<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        $procedure = "
        DROP PROCEDURE IF EXISTS `get_questions_test_by_topic`;
        CREATE PROCEDURE `get_questions_test_by_topic` (
            IN `topic_uuids` LONGTEXT,
            IN `id_oposicion` VARCHAR(36),
            IN `id_usuario` VARCHAR(36),
            IN `n_pregs` INT
        )
        BEGIN
    DECLARE index_loop INTEGER;
    DECLARE npregs_by_topic INTEGER;
    DECLARE c INTEGER;
    DECLARE i INTEGER;
    DECLARE num_preguntas INTEGER;
    DECLARE questions_by_topic INTEGER;
    DECLARE v_done INTEGER DEFAULT FALSE;
    DECLARE v_id VARCHAR(36);
    DECLARE cur1 CURSOR FOR SELECT topic_uuid FROM tmp_topics_selected;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;


    DROP TEMPORARY TABLE IF EXISTS tmp_topics;
    CREATE TEMPORARY TABLE tmp_topics (
        topic_id LONGTEXT,
        topic_uuid VARCHAR(36),
        nombre_del_tema VARCHAR(255),
        total_questions INT
    );

    DROP TEMPORARY TABLE IF EXISTS tmp_topics_selected;
    CREATE TEMPORARY TABLE tmp_topics_selected (
        topic_uuid VARCHAR(36),
        total_questions INT
    );

    DROP TEMPORARY TABLE IF EXISTS tmp_selected_questions;
    CREATE TEMPORARY TABLE tmp_selected_questions (
        question_id VARCHAR(36)
    );

    SET num_preguntas := n_pregs;
INSERT INTO tmp_topics (topic_id, topic_uuid, nombre_del_tema, total_questions)
    SELECT
    TB.topic_id as topic_id,
    TB.topic_id as topic_uuid,
    TB.name_topic AS nombre_del_tema,
    COUNT(id_q) as total_questions
FROM(
    SELECT DISTINCT
        q.questionable_id as topic_id,
        t.name as name_topic,
        '' as subtopic_id,
        q.id as id_q
    FROM questions q
    INNER JOIN topics t on t.id = q.questionable_id
    INNER JOIN oppositionables op ON op.oppositionable_id = q.questionable_id
    WHERE op.opposition_id = id_oposicion
    AND q.questionable_type = 'App\\Models\\Topic'
    UNION
    SELECT DISTINCT
        st.topic_id as topic_id,
        t.name as name_topic,
        q.questionable_id as subtopic_id,
        q.id
    FROM questions q
    INNER JOIN subtopics st on q.questionable_id = st.id
    INNER JOIN topics t on t.id = st.topic_id
    INNER JOIN oppositionables op ON op.oppositionable_id = st.id
    WHERE op.opposition_id = id_oposicion
    AND q.questionable_type = 'App\\Models\\Subtopic'
    ) as TB
    WHERE
        FIND_IN_SET(TB.topic_id, topic_uuids) > 0
        AND TB.topic_id IN (
            SELECT
                t2.id
            FROM
                topics t2
                INNER JOIN oppositionables o2 ON o2.oppositionable_id = t2.id
            WHERE
                o2.opposition_id = id_oposicion
        )
    GROUP BY topic_id
    ORDER BY total_questions ASC;

    IF (SELECT COUNT(*) FROM tmp_topics) > num_preguntas THEN
        INSERT INTO tmp_topics_selected
        SELECT topic_uuid, total_questions FROM tmp_topics
        ORDER BY RAND()
        LIMIT num_preguntas;
    ELSE
        INSERT INTO tmp_topics_selected
        SELECT topic_uuid, total_questions FROM tmp_topics;
    END IF;

    SET npregs_by_topic = (SELECT count(*) FROM tmp_topics);
    SET npregs_by_topic := num_preguntas / npregs_by_topic;
    SET index_loop:=0;

    OPEN cur1;

    read_loop: LOOP
        FETCH cur1 INTO v_id;
        IF v_done THEN
          LEAVE read_loop;
        END IF;

        DROP TEMPORARY TABLE IF EXISTS tmp_table;

        CREATE TEMPORARY TABLE tmp_table
          SELECT DISTINCT q.question_id
          FROM questions_used_test q
          INNER JOIN questions qu ON qu.id = q.question_id
          WHERE topic_id = v_id
          AND user_id = id_usuario
          AND opossition_id = id_oposicion
          AND result = 0
          AND qu.is_visible = 'yes';

        INSERT INTO  tmp_table
            SELECT distinct q.id
            FROM questions q
            INNER JOIN oppositionables op ON op.oppositionable_id = q.questionable_id
            WHERE q.id NOT IN (
                SELECT q.question_id
                FROM questions_used_test q
                INNER JOIN questions qu ON qu.id = q.question_id
                WHERE topic_id = v_id
                AND user_id = id_usuario
                AND opossition_id = id_oposicion
                AND qu.is_visible = 'yes')
              AND op.opposition_id = id_oposicion
              AND q.questionable_id = v_id
              AND q.questionable_type = 'App\\Models\\Topic'
              AND q.question_in_edit_mode = 'no'
              AND q.is_visible = 'yes'
              AND q.its_for_test = 'yes';

        INSERT INTO  tmp_table
            SELECT distinct q.id
            FROM questions q
              INNER JOIN subtopics st ON st.id = q.questionable_id
              INNER JOIN oppositionables op ON op.oppositionable_id = st.id
            WHERE q.id NOT IN (
                SELECT q.question_id
                FROM questions_used_test q
                INNER JOIN questions qu ON qu.id = q.question_id
                WHERE topic_id = v_id
                AND user_id = id_usuario
                AND opossition_id = id_oposicion
                AND qu.is_visible = 'yes')
              AND op.opposition_id = id_oposicion
              AND st.topic_id = v_id
              AND q.questionable_type = 'App\\Models\\Subtopic'
              AND q.question_in_edit_mode = 'no'
              AND q.is_visible = 'yes'
              AND q.its_for_test = 'yes';

        SET c := (SELECT count(*) FROM tmp_table);

        IF c < npregs_by_topic THEN
            SET c := npregs_by_topic - c;
            /*En caso de que las preguntas no sea suficientes, añadimos también las que el usuario ya ha acertado*/
            INSERT INTO   tmp_table
              SELECT DISTINCT q.question_id
              FROM questions_used_test q
              INNER JOIN questions qu ON qu.id = q.question_id
              WHERE topic_id = v_id
              AND user_id = id_usuario
              AND opossition_id = id_oposicion
              AND result = 1
              AND qu.is_visible = 'yes'
              ORDER BY RAND() LIMIT c;
        END IF;

        INSERT INTO tmp_selected_questions
        SELECT question_id FROM tmp_table ORDER BY RAND() LIMIT npregs_by_topic;

        DROP TEMPORARY TABLE tmp_table;

        SET questions_by_topic = (SELECT COUNT(*) FROM tmp_selected_questions);

        SET index_loop := index_loop+1;
        SET num_preguntas := n_pregs - questions_by_topic;

        SET c:=(SELECT count(*) FROM tmp_topics_selected);

        IF (c- index_loop) > 0 THEN
        SET npregs_by_topic := num_preguntas / (c - index_loop);
        END IF;

    END LOOP;

    CLOSE cur1;

    SELECT * FROM tmp_selected_questions ORDER BY RAND();

    DROP TEMPORARY TABLE tmp_topics;
    DROP TEMPORARY TABLE tmp_topics_selected;
    DROP TEMPORARY TABLE tmp_selected_questions;
END";

        DB::unprepared($procedure);
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
        $procedure = "DROP PROCEDURE IF EXISTS `get_questions_test_by_topic";
        DB::unprepared($procedure);
    }
};
