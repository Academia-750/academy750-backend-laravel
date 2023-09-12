<?php


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        $this->call(UserSeeder::class);

        $this->call(TopicGroupSeeder::class);
        $this->call(TestTypeSeeder::class);
    }
}