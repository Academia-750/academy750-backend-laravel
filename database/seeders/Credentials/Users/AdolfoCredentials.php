<?php

namespace Database\Seeders\Credentials\Users;

use App\Core\Services\UserService;
use App\Core\Services\UserServiceTrait;
use App\Core\Services\UuidGeneratorService;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Credentials\Users\Interface\CredentialsInterface;

class AdolfoCredentials implements CredentialsInterface
{
    use UserServiceTrait;

    public static function AdminCredentials (): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User
    {
        $roleAdmin = Role::query()->where('name', '=', 'admin')->first();

        $adminAdolfo = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'email' => 'adolfoferia@gmail.com',
            'first_name' => 'Adolfo',
            'last_name' => 'Feria',
            'full_name' => 'Adolfo Feria',
            'dni' => "42711006Y",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('@UX!M54wQn'),
        ]);

        $adminAdolfo->assignRole($roleAdmin);

        $adminAdolfo->image()->create([
            'path' => '/storage/users/images/adolfo_feria.webp',
            'type_path' => 'local'
        ]);

        return $adminAdolfo;
    }

    public static function StudentCredentials(): \Illuminate\Database\Eloquent\Model|\LaravelIdea\Helper\App\Models\_IH_User_QB|\Illuminate\Database\Eloquent\Builder|User
    {
        $roleStudent = Role::query()->where('name', '=', 'student')->first();

        $studentAdolfo = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'email' => 'adolfoferia.student@academia750.com',
            'first_name' => 'Adolfo',
            'last_name' => 'Feria',
            'full_name' => 'Adolfo Feria',
            'dni' => "67239172Y",
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('zKY$MUM3KWRn9#'),
        ]);

        $studentAdolfo->assignRole($roleStudent);
        $studentAdolfo->image()->create([
            'path' => '/storage/users/images/adolfo_feria.webp',
            'type_path' => 'local'
        ]);

        return $studentAdolfo;
    }
}
