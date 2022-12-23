<?php

namespace Database\Seeders;

use App\Core\Services\UserServiceTrait;
use App\Core\Services\UuidGeneratorService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    use UserServiceTrait;

    public $factory;

    public function __construct()
    {
        $this->factory = Factory::create();
    }

    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        /* Super Admin */
        $AcademiaAccount = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Academia',
            'last_name' => 'Bomberos Principal',
            'full_name' => 'Academia Bomberos Principal',
            // 'email' => config('mail.from.address'),
            'email' => 'admin2@admin.com',
            'dni' => '16788280M',
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('GZVX4B)5PbD^aR'),
        ]);
        $AcademiaImpugnaciones = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Academia',
            'last_name' => 'Bomberos Impugnaciones',
            'full_name' => 'Academia Bomberos Impugnaciones',
            // 'email' => config('mail.mail_impugnaciones'),
            'email' => 'admin@admin.com',
            'dni' => '73314025F',
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('GZVX4B)5PbD^aR'),
        ]);

        /*Admin*/
        $adminAdolfo = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Adolfo',
            'email' => 'adolfoferia.admin@academia750.com',
            'last_name' => 'Feria',
            'full_name' => 'Adolfo Feria',
            'dni' => "42711006Y",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('admin'),
        ]);

        $adminRaul = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Raul Moheno',
            'email' => 'raulmoheno.admin@academia750.com',
            'last_name' => 'Moheno',
            'full_name' => 'Raul Moheno',
            'dni' => "32631674X",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('admin'),
        ]);

        /*Editor*/
        $studentAdolfo = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Adolfo',
            'email' => 'adolfoferia.student@academia750.com',
            'last_name' => 'Feria',
            'full_name' => 'Adolfo Feria',
            'dni' => "67239172Y",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('student'),
        ]);
        $studentRaul = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Raul Moheno',
            'email' => 'raulmoheno.student@academia750.com',
            'last_name' => 'Moheno',
            'full_name' => 'Raul Moheno',
            'dni' => "14071663X",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('student'),
        ]);

        $roleAdmin = Role::query()->where('name', '=', 'admin')->first();
        $roleStudent = Role::query()->where('name', '=', 'student')->first();
        $roleSuperAdmin = Role::query()->where('name', '=', 'super-admin')->first();

        /*Assign Role*/
        $adminAdolfo->assignRole($roleAdmin);
        $adminRaul->assignRole($roleAdmin);

        $studentAdolfo->assignRole($roleStudent);
        $studentRaul->assignRole($roleStudent);

        $AcademiaAccount->assignRole($roleSuperAdmin);
        $AcademiaImpugnaciones->assignRole($roleSuperAdmin);

        $adminAdolfo->image()->create([
            'path' => 'https://via.placeholder.com/128.webp',
            'type_path' => 'url'
        ]);

        $adminAdolfo->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Adolfo+Feria+Admin',
            'type_path' => 'url'
        ]);
        $adminRaul->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Raul+Moheno+Admin',
            'type_path' => 'url'
        ]);

        $studentAdolfo->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Adolfo+Feria+Student',
            'type_path' => 'url'
        ]);
        $studentRaul->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Raul+Moheno+Student',
            'type_path' => 'url'
        ]);

        $AcademiaAccount->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Academia750',
            'type_path' => 'url'
        ]);
        $AcademiaImpugnaciones->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Academia750+Impugnaciones',
            'type_path' => 'url'
        ]);

        User::factory()->count(10)->create()->each(static function ($itemModel) use ($roleStudent) {
            $itemModel->assignRole($roleStudent);
            $itemModel->image()->create([
                'path' => 'https://via.placeholder.com/128.webp?text=ExampleUser',
                'type_path' => 'url'
            ]);
        });
        User::factory()->count(5)->create()->each(static function ($itemModel) use ($roleStudent) {
            $itemModel->assignRole($roleStudent);
            $itemModel->state = 'disable';
            $itemModel->save();
            $itemModel->image()->create([
                'path' => 'https://via.placeholder.com/128.webp?text=ExampleUser',
                'type_path' => 'url'
            ]);
        });
    }
}
