<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\trait\RegisterPermissionsTrait;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    use RegisterPermissionsTrait;

    public Role $student;
    public Role $admin;
    public Role $superAdmin;

    public function __construct()
    {
        $this->student = Role::query()->where('name', '=', 'student')
            ->first();
        $this->admin = Role::query()->where('name', '=', 'admin')
            ->first();
        $this->superAdmin = Role::query()->where('name', '=', 'super-admin')
            ->first();
    }

    public function run(): void
    {
        /* ---------------------------------------------------------------------------------------------------- */
        /* -------------- USERS - STUDENTS -----------------------*/

        $this->permissionsUsersByRoleStudent();

        /* ----------------------------------------------------------------------------------------------------
        /* ------------------------------- OPOSICIONES ---------------------------- */
        $this->permissionsOppositions();

        /* ------------------------- TEMAS --------------------------- */

        $this->permissionsTopics();
        $this->permissionsResourceTopic();

        /* --------------- SUBTEMAS ------------------ */

        $this->permissionSubtopics();

        /* ----------------------- CUESTIONARIOS ------------------------*/

        $this->permissionsTestsStudent();

        $this->superAdmin->syncPermissions(Permission::all());
    }
}
