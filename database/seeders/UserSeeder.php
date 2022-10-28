<?php

namespace Database\Seeders;

use App\Core\Services\UserServiceTrait;
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

    public function run(): void
    {
        $factory = Factory::create();

        /*Admin*/
        $adminAdolfo = User::query()->create([
            'id' => $this->getUUIDUnique(),
            'first_name' => 'Adolfo Feria',
            'email' => 'adolfoferia.admin@academia750.com',
            'last_name' => $factory->lastName(),
            'dni' => "42711006Y",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('admin'),
        ]);

        $adminRaul = User::query()->create([
            'id' => $this->getUUIDUnique(),
            'first_name' => 'Raul Moheno',
            'email' => 'raulmoheno.admin@academia750.com',
            'last_name' => $factory->lastName(),
            'dni' => "32631674X",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('admin'),
        ]);

        /*Editor*/
        $studentAdolfo = User::query()->create([
            'id' => $this->getUUIDUnique(),
            'first_name' => 'Adolfo Feria',
            'email' => 'adolfoferia.student@academia750.com',
            'last_name' => $factory->lastName(),
            'dni' => "67239172Y",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('student'),
        ]);
        $studentRaul = User::query()->create([
            'id' => $this->getUUIDUnique(),
            'first_name' => 'Raul Moheno',
            'email' => 'raulmoheno.student@academia750.com',
            'last_name' => $factory->lastName(),
            'dni' => "14071663X",
            'phone' => $this->getNumberPhoneSpain(),
            'password' => bcrypt('student'),
        ]);

        /*Assign Role*/
        $adminAdolfo->assignRole('admin');
        $adminRaul->assignRole('admin');

        $studentAdolfo->assignRole('student');
        $studentRaul->assignRole('student');
    }
}
