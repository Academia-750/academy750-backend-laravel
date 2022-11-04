<?php

namespace Database\Seeders;

use App\Core\Services\UserServiceTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use UserServiceTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::query()->create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'admin',
                'alias_name' => 'Administrador',
            ]
        );
        Role::query()->create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'student',
                'alias_name' => 'Estudiante',
            ]
        );
    }
}
