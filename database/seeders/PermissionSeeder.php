<?php



use App\Models\Permission;
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
        ['category' => 'lesson', 'name' => Permission::SEE_LESSONS, 'alias_name' => 'Acceso al menu de clases'],
        ['category' => 'lesson', 'name' => Permission::JOIN_LESSONS, 'alias_name' => 'Confirmar asistencia a clase'],
        ['category' => 'lesson', 'name' => Permission::SEE_ONLINE_LESSON, 'alias_name' => 'Acceso al aula virtual'],
        ['category' => 'lesson', 'name' => Permission::SEE_LESSON_MATERIALS, 'alias_name' => 'Acceso a los materiales de la lección'],
        ['category' => 'lesson', 'name' => Permission::SEE_LESSON_RECORDINGS, 'alias_name' => 'Acceso a las grabaciones de la lección'],
        ['category' => 'lesson', 'name' => Permission::SEE_LESSON_PARTICIPANTS, 'alias_name' => 'Acceso al listado de asistencia de la lección'],

        // Tests
        ['category' => 'tests', 'name' => Permission::GENERATE_TESTS, 'alias_name' => 'Acceso al menu de tests']
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