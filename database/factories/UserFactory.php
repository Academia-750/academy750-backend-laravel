<?php

namespace Database\Factories;

use App\Core\Services\UserServiceTrait;
use App\Core\Services\UuidGeneratorService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserFactory extends Factory
{
    use UserServiceTrait;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'uuid' => UuidGeneratorService::getUUIDUnique(User::class),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'email' => $this->faker->unique()->safeEmail(),
            'dni' => $this->generateDNIUnique(),
            'phone' => $this->getNumberPhoneSpain(),
            'state' => 'enable',
            'password' => Hash::make('Zd8jNT!8P*G'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function student(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) {
            $user->assignRole('student');
        });
    }
    public function withRole($role): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) use ($role) {
            $user->assignRole($role);
        });
    }

    public function allowedTo($permissions): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (User $user) use ($permissions) {
            $user->givePermissionTo($permissions);
        });
    }

}