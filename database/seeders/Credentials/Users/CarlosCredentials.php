<?php

namespace Database\Seeders\Credentials\Users;

use App\Core\Services\UserService;
use App\Core\Services\UuidGeneratorService;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Credentials\Users\Interface\CredentialsInterface;

class CarlosCredentials implements CredentialsInterface
{

    public static function AdminCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User
    {
        $roleAdmin = Role::query()->where('name', '=', 'admin')->first();

        $adminCarlos = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'email' => 'cehojac@gmail.com',
            'first_name' => 'Carlos',
            'last_name' => 'Herrera',
            'full_name' => 'Carlos Herrera',
            'dni' => "94344041L",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('54P%$VviB'),
        ]);

        $adminCarlos->assignRole($roleAdmin);

        $adminCarlos->image()->create([
            'path' => '/storage/users/images/carlos_herrera.webp',
            'type_path' => 'local'
        ]);

        return $adminCarlos;
    }

    public static function StudentCredentials(): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User
    {
        $roleStudent = Role::query()->where('name', '=', 'student')->first();

        $studentCarlos = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'email' => 'carlosherrera.student@academia750.com',
            'first_name' => 'Carlos',
            'last_name' => 'Herrera',
            'full_name' => 'Carlos Herrera',
            'dni' => "41426213Q",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('xF7@EGm$UZ3'),
        ]);

        $studentCarlos->assignRole($roleStudent);
        $studentCarlos->image()->create([
            'path' => '/storage/users/images/carlos_herrera.webp',
            'type_path' => 'local'
        ]);

        return $studentCarlos;
    }
}
