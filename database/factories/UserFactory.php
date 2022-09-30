<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "identification_number" => $this->obtener_documento_unico(),
            'name' => $this->faker->name(),
            'last_name' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'number_phone' => $this->faker->unique()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function obtener_documento_unico()
    {
        $documento_identidad = $this->generar_documento_identidad();
        $existe_documento_identidad = User::query()->where('identification_number', '=', $documento_identidad)->count();

        // Siempre que encuentre un documento, va a seguir generando más hasta generar uno que no coincida en la tabla
        while ($existe_documento_identidad !== 0) {
            $documento_identidad = $this->generar_documento_identidad();
        }

        return $documento_identidad;
    }

    public function generar_documento_identidad($longitud = 8)
    {
        $documento = '';
        for ($iterator = 1; $iterator <= $longitud; $iterator++) {

            $documento.=random_int(1,9);

        }
        $numeros=intval($documento);
        $letra=substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1);
        $documento.=$letra;
        return $documento;
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
