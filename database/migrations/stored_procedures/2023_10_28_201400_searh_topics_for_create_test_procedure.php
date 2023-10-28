<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function __construct(public string $nameProcedure = 'topics_available_for_create_test_procedure')
    {
    }

    public function up()
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN oposicion_id INT,
            IN grupos_ids TEXT
        )
        BEGIN
            SELECT B.id, COUNT(*) as 'cantidad'
                FROM oppositionables A
            INNER JOIN subtopics C ON C.id = A.oppositionable_id
            INNER JOIN topics B ON B.id = C.topic_id
            INNER JOIN questions D ON D.questionable_id = C.id
                WHERE A.opposition_id = oposicion_id
                AND A.oppositionable_type = 'App\\Models\\Subtopic'
                AND D.questionable_type = 'App\\Models\\Subtopic'
                AND FIND_IN_SET(B.topic_group_id, grupos_ids) > 0
                AND B.is_available = 'yes'
                AND C.is_available = 'yes'
                AND D.is_visible = 'yes'
            GROUP BY B.id
                HAVING COUNT(*) > 0
            UNION
                SELECT B.id, COUNT(*) as 'cantidad'
                FROM oppositionables A
            INNER JOIN topics B ON A.oppositionable_id = B.id
            INNER JOIN questions C ON C.questionable_id = B.id
                WHERE A.opposition_id = oposicion_id
                AND A.oppositionable_type = 'App\\Models\\Topic'
                AND C.questionable_type = 'App\\Models\\Topic'
                AND FIND_IN_SET(B.topic_group_id, grupos_ids) > 0
                AND B.is_available = 'yes'
                AND C.is_visible = 'yes'
            GROUP BY B.id
            HAVING COUNT(*) > 0;
            END
            ";

        DB::unprepared($procedure);
    }

    public function down()
    {
        $procedure = "DROP PROCEDURE IF EXISTS {$this->nameProcedure}";
        DB::unprepared($procedure);
    }
};
