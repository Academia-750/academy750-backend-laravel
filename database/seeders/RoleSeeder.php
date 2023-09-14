<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::query()->create(
            [
                'name' => 'admin',
                'alias_name' => 'Administrador',
                'protected' => 1,
            ]
        );
        Role::query()->create(
            [
                'name' => 'student',
                'alias_name' => 'Estudiante',
                'default_role' => 1
            ]
        );
    }
}