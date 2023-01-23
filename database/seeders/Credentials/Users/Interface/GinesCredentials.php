<?php

namespace Database\Seeders\Credentials\Users\Interface;

use App\Core\Services\UserService;
use App\Core\Services\UserServiceTrait;
use App\Core\Services\UuidGeneratorService;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Credentials\Users\Interface\CredentialsInterface;

class GinesCredentials implements  CredentialsInterface
{
    use UserServiceTrait;

    public static function AdminCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User {
        $roleAdmin = Role::query()->where('name', '=', 'admin')->first();

        $adminGines = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'email' => 'gines.rabasco.admin@academia750.com',
            'first_name' => 'Gines',
            'last_name' => 'Rabasco',
            'full_name' => 'Gines Rabasco',
            'dni' => "74237694L",
            'phone' => '660801968',
            'password' => bcrypt('academia750'),
        ]);

        $adminGines->assignRole($roleAdmin);
/*
        $adminGines->image()->create([
            'path' => '/storage/users/images/Gines_moheno.webp',
            'type_path' => 'local'
        ]);*/

        return $adminGines;
    }

    public static function StudentCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User {
        $roleStudent = Role::query()->where('name', '=', 'student')->first();

        $studentGines = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'email' => 'gines.rabasco.student@academia750.com',
            'first_name' => 'Gines',
            'last_name' => 'Moheno',
            'full_name' => 'Gines Rabasco',
            'dni' => "10668102N",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('academia750'),
        ]);

        $studentGines->assignRole($roleStudent);

        /*$studentGines->image()->create([
            'path' => '/storage/users/images/Gines_moheno.webp',
            'type_path' => 'local'
        ]);*/

        return $studentGines;
    }
}
