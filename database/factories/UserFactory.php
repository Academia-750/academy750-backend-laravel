<?php

namespace Database\Factories;

use App\Core\Services\UserServiceTrait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class UserFactory extends Factory
{
    use UserServiceTrait;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape(['id' => "\Ramsey\Uuid\UuidInterface", 'first_name' => "string", 'last_name' => "string", 'email' => "string", 'dni' => "mixed", 'phone' => "string", 'password' => "mixed", 'email_verified_at' => "\Illuminate\Support\Carbon", 'remember_token' => "string"])] public function definition(): array
    {
        return [
            'id' => $this->getUUIDUnique(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'dni' => $this->generateDNIUnique(),
            'phone' => $this->getNumberPhoneSpain(),
            'password' => Hash::make('academia750'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
