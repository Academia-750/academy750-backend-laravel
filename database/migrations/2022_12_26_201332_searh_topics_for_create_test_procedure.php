<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "DROP PROCEDURE IF EXISTS `topics_available_for_create_test`;
        CREATE PROCEDURE `topics_available_for_create_test`(
            IN oposicion_id VARCHAR(255),
            IN grupo_id VARCHAR(255)
        )
        BEGIN
        SELECT t.id, count(*) as 'cantidad'
        FROM topics t, subtopics s, questions p, oppositionables o
        WHERE t.id=s.topic_id and (o.oppositionable_id=t.id or o.oppositionable_id=s.id) AND
         (p.questionable_id=t.id or p.questionable_id=s.id) and o.opposition_id=oposicion_id and
          t.topic_group_id=grupo_id and p.is_visible='yes'
          group by t.id having count(*)>0
UNION
SELECT t2.id, count(*) as 'cantidad'
        FROM topics t2, questions p2, oppositionables o2
        WHERE o2.oppositionable_id=t2.id AND p2.questionable_id=t2.id  and o2.opposition_id=oposicion_id and
          t2.topic_group_id=grupo_id and p2.is_visible='yes'
          group by t2.id having count(*)>0;
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
        $procedure= "DROP PROCEDURE IF EXISTS `topics_available_for_create_test`";
        DB::unprepared($procedure);
    }
};
