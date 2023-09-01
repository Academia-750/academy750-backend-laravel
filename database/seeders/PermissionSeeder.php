<?php

namespace Database\Seeders;


use App\Models\Permission;
use Illuminate\Database\Seeder;


/**
 * All permissions are register here.
 * This seeder is migrated each time you want to update the permissions in local
 *  > php artisan db:seed --class=PermissionSeeder
 *
 * The deployment pipelines will also run this script each time for an automatic create / update
 * You will need to run migrations in order to delete permissions
 */
class PermissionSeeder extends Seeder
{

    public static $permissions = [
        // Lessons
        ['category' => 'lesson', 'name' => 'see-lessons', 'alias_name' => 'Ver clases'],
        ['category' => 'lesson', 'name' => 'join-lessons', 'alias_name' => 'Apuntar a clase'],
        ['category' => 'lesson', 'name' => 'online-lessons', 'alias_name' => 'Clases online'],
        ['category' => 'lesson', 'name' => 'material-lessons', 'alias_name' => 'Materiales de Clases'],
        ['category' => 'lesson', 'name' => 'recording-lessons', 'alias_name' => 'Grabaciones de Clases'],
        ['category' => 'lesson', 'name' => 'participants-lessons', 'alias_name' => 'Listado de Asistentes'],

        // Tests
        ['category' => 'tests', 'name' => 'generate-tests', 'alias_name' => 'Generar Tests']
    ];

    private $categories = [
        'lessons' => []
    ];


    public function __construct()
    {


    }

    public function run(): void
    {
        array_map(function ($permission) {
            $this->registerPermission($permission);
        }, self::$permissions);
    }

    private function existsPermission($permission_name): bool
    {
        return Permission::query()->where('name', '=', $permission_name)->count() > 0;
    }

    private function registerPermission($data)
    {
        if ($this->existsPermission($data['name'])) {
            Permission::query()->where('name', $data['name'])->update([
                'alias_name' => $data['alias_name'],
                'category' => $data['category'],
            ]);
            return;
        }

        Permission::query()->create([
            'name' => $data['name'],
            'alias_name' => $data['alias_name'],
            'category' => $data['category'],
        ]);
    }

}