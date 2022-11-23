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

        /*Admin*/
        $adminAdolfo = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Adolfo Feria',
            'email' => 'adolfoferia.admin@academia750.com',
            'last_name' => $this->factory->lastName(),
            'dni' => "42711006Y",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('admin'),
        ]);

        $adminRaul = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Raul Moheno',
            'email' => 'raulmoheno.admin@academia750.com',
            'last_name' => $this->factory->lastName(),
            'dni' => "32631674X",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('admin'),
        ]);

        /*Editor*/
        $studentAdolfo = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Adolfo Feria',
            'email' => 'adolfoferia.student@academia750.com',
            'last_name' => $this->factory->lastName(),
            'dni' => "67239172Y",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('student'),
        ]);
        $studentRaul = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Raul Moheno',
            'email' => 'raulmoheno.student@academia750.com',
            'last_name' => $this->factory->lastName(),
            'dni' => "14071663X",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('student'),
        ]);

        $roleAdmin = Role::query()->where('name', '=', 'admin')->first();
        $roleStudent = Role::query()->where('name', '=', 'student')->first();

        /*Assign Role*/
        $adminAdolfo->assignRole($roleAdmin);
        $adminRaul->assignRole($roleAdmin);

        $studentAdolfo->assignRole($roleStudent);
        $studentRaul->assignRole($roleStudent);

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

        User::factory()->count(10)->create()->each(static function ($itemModel) use ($roleStudent) {
            $itemModel->assignRole($roleStudent);
            $itemModel->image()->create([
                'path' => 'https://via.placeholder.com/128.webp?text=ExampleUser',
                'type_path' => 'url'
            ]);
        });
        User::factory()->count(10)->create()->each(static function ($itemModel) use ($roleStudent) {
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
