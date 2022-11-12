<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);

        $this->call(StudentSeeder::class);
        $this->call(OppositionSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(SubtopicSeeder::class);
        $this->call(TopicGroupSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(TestSeeder::class);
        $this->call(AnswerSeeder::class);
        // [EndOfLineMethodRegister]
    }
}
