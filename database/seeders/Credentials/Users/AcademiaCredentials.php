<?php

namespace Database\Seeders\Credentials\Users;

use App\Core\Services\UserService;
use App\Core\Services\UuidGeneratorService;
use App\Models\Role;
use App\Models\User;

class AcademiaCredentials
{
    public static function academiaAccount (): void {
        $roleSuperAdmin = Role::query()->where('name', '=', 'super-admin')->first();
        $AcademiaAccount = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Academia',
            'last_name' => 'Bomberos Principal',
            'full_name' => 'Academia Bomberos Principal',
            'email' => config('mail.from.address'),
            //'email' => 'admin2@admin.com',
            'dni' => '123456789',
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('GZVX4B)5PbD^aR'),
        ]);

        $AcademiaAccount->assignRole($roleSuperAdmin);

        $AcademiaAccount->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Academia750',
            'type_path' => 'url'
        ]);


    }

    public static function academiaImpugnaciones (): void {
        $roleSuperAdmin = Role::query()->where('name', '=', 'super-admin')->first();
        $AcademiaImpugnaciones = User::query()->create([
            'id' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Academia',
            'last_name' => 'Bomberos Impugnaciones',
            'full_name' => 'Academia Bomberos Impugnaciones',
            'email' => config('mail.mail_impugnaciones'),
            //'email' => 'admin@admin.com',
            'dni' => '987654321',
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('GZVX4B)5PbD^aR'),
        ]);

        $AcademiaImpugnaciones->assignRole($roleSuperAdmin);

        $AcademiaImpugnaciones->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Academia750+Impugnaciones',
            'type_path' => 'url'
        ]);
    }
}
