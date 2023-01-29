<?php

namespace Database\Seeders\Credentials\Users;

use App\Core\Services\UserService;
use App\Core\Services\UserServiceTrait;
use App\Core\Services\UuidGeneratorService;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Credentials\Users\Interface\CredentialsInterface;

class RaulCredentials implements  CredentialsInterface
{
    use UserServiceTrait;

    public static function AdminCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User {
        $roleAdmin = Role::query()->where('name', '=', 'admin')->first();

        $adminRaul = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            //'email' => 'raulmoheno.admin@academia750.com',
            'email' => 'springh.trap@gmail.com',
            'first_name' => 'Raul',
            'last_name' => 'Moheno',
            'full_name' => 'Raul Moheno',
            'dni' => "32631674X",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('g5UZXCHJ5Zm#AB!'),
        ]);

        $adminRaul->assignRole($roleAdmin);

        $adminRaul->image()->create([
            'path' => '/storage/users/images/raul_moheno.webp',
            'type_path' => 'local'
        ]);

        return $adminRaul;
    }

    public static function StudentCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User {
        $roleStudent = Role::query()->where('name', '=', 'student')->first();

        $studentRaul = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            //'email' => 'raulmoheno.student@academia750.com',
            'email' => 'ramz.162025@gmail.com',
            'first_name' => 'Raul',
            'last_name' => 'Moheno',
            'full_name' => 'Raul Moheno',
            'dni' => "14071663X",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('EcsN9HYA9)&'),
        ]);

        $studentRaul->assignRole($roleStudent);

        $studentRaul->image()->create([
            'path' => '/storage/users/images/raul_moheno.webp',
            'type_path' => 'local'
        ]);

        return $studentRaul;
    }
}
