<?php

namespace Database\Seeders;

use App\Core\Services\UserServiceTrait;
use App\Core\Services\UuidGeneratorService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Credentials\Users\RegisterCredentials;
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

        RegisterCredentials::registerCredentials();

        $roleStudent = Role::query()->where('name', '=', 'student')->first();

        User::factory()->count(5)->create()->each(static function ($itemModel) use ($roleStudent) {
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
