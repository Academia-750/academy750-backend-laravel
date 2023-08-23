<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * This script allows us to migrate the database v1 (where the UUID is the PK)
 * to the version 2, where the PK is an auto increment number.
 *
 * BEFORE running make sure that the database you are migrating to has all the
 * migrations executed:
 *  `DB_DATABASE=$NAME php artisan migrate `
 *
 * This script is doing 2 things:
 *  - Clone the tables that has no data change
 *  - Migrate the tables id (uuid) -> to id (int) uuid (copy of the old id)
 *  - Fiding the new PK id for each PK that before was an UUID
 */
class MigrateDBv2 extends Command
{
    use helpersJsonApi;

    protected $filesystem;
    protected $signature = 'migrate:dbv2
                            {old : Old database }
                            {new : Database output of the operation }';

    protected $description = 'Migrate a database v1 (where UUID are primary key) to a database v2 (where the ID is the primary key)';

    private $old_database;
    private $new_database;

    private $morph_to_table = [
        "App\Models\Topic" => 'topics',
        "App\Models\Subtopic" => 'subtopics',
        "App\Models\User" => 'users'
    ];

    private function getPDO($database)
    {
        $config = config('database.connections')['mysql'];

        $dns = $config['driver'] . ":dbname={$database};host={$config['host']}";

        return new \PDO(
            $dns, $config['username'], $config['password'], $config['options']
        );
    }

    private function init()
    {
        $this->old_database = clone DB::connection()->setPdo($this->getPDO($this->argument('old')));
        $this->new_database = clone DB::connection()->setPdo($this->getPDO($this->argument('new')));
    }

    private function old($table)
    {
        return $this->old_database->table($table);
    }

    private function new($table = '')
    {
        return $this->new_database->table($table);

    }



    public function handle(): void
    {

        $this->init(); // Set up connections
        // $this->old = DB::connection()->setPdo($this->getPDO($this->argument('old')));

        $this->new_database->statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->clone('migrations');

        $this->clone('failed_jobs');
        $this->clone('import_processes');
        $this->clone('import_records');
        $this->clone('jobs');

        $this->migrateTable('images', ['imageable_id' => '$imageable_type']);
        $this->migrateTable('notifications', ['notifiable_id' => '$notifiable_type']);

        $this->clone('password_resets');
        $this->migrateTable('personal_access_tokens', ['tokenable_id' => '$tokenable_type']);

        $this->clone('roles');
        $this->clone('permissions');
        $this->migrateTable('role_has_permissions', ['role_id' => 'roles', 'permission_id' => 'permissions']);
        $this->migrateTable('model_has_roles', ['role_id' => 'roles', 'model_id' => '$model_type']);

        $this->migrateTable('topic_groups');
        $this->migrateTable('topics', ['topic_group_id' => 'topic_groups']);
        $this->migrateTable('subtopics', ['topic_id' => 'topics']);

        $this->migrateTable('oppositions');
        $this->migrateTable('oppositionables', ['opposition_id' => 'oppositions', 'oppositionable_id' => '$oppositionable_type']);
        $this->migrateTable('questions', ['questionable_id' => '$questionable_type']);
        $this->migrateTable('answers', ['question_id' => 'questions']);

        $this->migrateTable('users');

        $this->migrateTable('test_types');

        $this->migrateTable('tests', ['opposition_id' => 'oppositions', 'user_id' => 'users']);
        $this->migrateTable('question_test', ['test_id' => 'tests', 'question_id' => 'questions', 'answer_id' => 'answers']);
        $this->migrateTable('questions_used_test', ['topic_id' => 'topics', 'subtopic_id' => 'subtopics', 'question_id' => 'questions', 'opposition_id' => 'oppositions']);






        $this->new_database->statement('SET FOREIGN_KEY_CHECKS=1;');
    }


    private function migrateTable($table, $uuid = [])
    {

        $this->new($table)->truncate();

        $count = $this->old($table)->limit(1000)->get()->count();

        $bar = $this->output->createProgressBar($count);
        $orphans = 0;
        $this->line(" - {$table}: {$count}");

        $bar->start();

        $page = 0;
        dump($page);
        $items = $this->old($table)->limit(20)->offset(20 * $page)->get();

        do {

            $items = $this->old($table)->limit(20)->offset(20 * $page)->get();

            foreach ($items as $item) {
                $ok = $this->insertItem($table, (array) $item, $uuid);
                !$ok ? $orphans++ : 0;
                $bar->advance();
            }
            $page++;
        } while (count($items) > 0);

        if ($orphans > 0) {
            $this->newLine();
            $this->line("{$table}: Orphans {$orphans}");
            $totalInserted = $this->new($table)->count();
            $this->line("{$table}: Completed {$totalInserted}");

        }


        $this->newLine();
    }

    private function clone ($table)
    {
        $this->new($table)->truncate();

        $count = $this->old($table)->get()->count();
        $bar = $this->output->createProgressBar($count);
        $this->line(" - {$table}: {$count}");

        $bar->start();

        for ($page = 0; $page < ($count / 20) + 1; $page++) {

            $items = $this->old($table)->limit(20)->offset(20 * $page)->get();

            foreach ($items as $item) {
                $this->new($table)->insert((array) $item);
                $bar->advance();
            }
        }

        $this->newLine();
    }


    private function insertItem($table, $item, $uuid)
    {
        if (Schema::hasColumn($table, 'uuid')) {
            $item['uuid'] = $item['id'];
        }

        if (isset($item['id'])) {
            unset($item['id']);
        }


        foreach ($uuid as $key => $table_origin) {

            if ($table_origin[0] == '$') {
                $field = substr($table_origin, 1);
                $table_origin = $this->morph_to_table[$item[$field]];
            }

            $uuid_column = Schema::hasColumn($table_origin, 'uuid') ? 'uuid' : 'id';

            $parent = $this->new($table_origin)->where($uuid_column, $item[$key])->first();
            if (!$parent) {
                return false;
            }

            $item[$key] = $parent->id;
        }

        return $this->new($table)->insert($item);

    }
}