<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function __construct(Public string $nameProcedure = 'topics_available_for_create_test_procedure'){}

    public function up()
    {
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN oposicion_id INT,
            IN grupo_id INT
        )
        BEGIN
            SELECT
              t.id,
              COUNT(*) as 'cantidad'
            FROM
              topics t,
              subtopics s,
              questions p,
              oppositionables o
            WHERE
              t.id = s.topic_id
              AND (
                o.oppositionable_id = t.id
                OR o.oppositionable_id = s.id
              )
              AND (
                p.questionable_id = t.id
                OR p.questionable_id = s.id
              )
              AND o.opposition_id = oposicion_id
              AND FIND_IN_SET(t.topic_group_id, grupos_ids) > 0
              AND t.is_available = 'yes'
              AND p.is_visible = 'yes'
            GROUP BY
              t.id
            HAVING
              COUNT(*) > 0
            UNION
            SELECT
              t2.id,
              COUNT(*) as 'cantidad'
            FROM
              topics t2,
              questions p2,
              oppositionables o2
            WHERE
              o2.oppositionable_id = t2.id
              AND p2.questionable_id = t2.id
              AND o2.opposition_id = oposicion_id
              AND FIND_IN_SET(t2.topic_group_id, grupos_ids) > 0
              AND t2.is_available = 'yes'
              AND p2.is_visible = 'yes'
            GROUP BY
              t2.id
            HAVING
              COUNT(*) > 0;
            END
            ";

        DB::unprepared($procedure);
    }

    public function down()
    {
        $procedure= "DROP PROCEDURE IF EXISTS {$this->nameProcedure}";
        DB::unprepared($procedure);
    }
};
