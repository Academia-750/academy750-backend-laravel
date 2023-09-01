<?php

namespace Database\Seeders;

use App\Core\Services\UserService;
use App\Core\Services\UserServiceTrait;
//use App\Core\Services\UuidGeneratorService;
//use App\Models\Permission;
use App\Core\Services\UuidGeneratorService;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Credentials\Users\RegisterCredentials;
//use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    use UserServiceTrait;

    public $factory;

    public function __construct()
    {
    }

    public static function academiaAccount(): void
    {
        $roleAdmin = Role::query()->where('name', '=', 'super-admin')->first();
        $account = User::query()->create([
            'uuid' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Academia',
            'last_name' => 'Bomberos Principal',
            'full_name' => 'Academia Bomberos Principal',
            'email' => config('mail.from.address'),
            //'email' => 'admin2@admin.com',
            'dni' => '00000000T',
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('academia750'),

        ]);

        $account->assignRole($roleAdmin);

        $account->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Academia750',
            'type_path' => 'url'
        ]);


    }

    /**
     * TODO: Remove this user and send the email directly without notification
     */
    public static function academiaQuestionClaims(): void
    {
        $account = User::query()->create([
            'uuid' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => 'Academia',
            'last_name' => 'Bomberos Impugnaciones',
            'full_name' => 'Academia Bomberos Impugnaciones',
            'email' => config('mail.mail_impugnaciones'),
            'dni' => '00000001R',
            'phone' => UserService::getNumberPhoneSpain(),
            'password' => bcrypt('GZVX4B)5PbD^aR'),
        ]);


        $account->image()->create([
            'path' => 'https://via.placeholder.com/128.webp?text=Academia750+Impugnaciones',
            'type_path' => 'url'
        ]);
    }
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->academiaAccount();
        $this->academiaQuestionClaims();
    }
}