<?php

namespace Tests\Feature\Students\v1\updateStudents;

use App\Core\Services\UserServiceTrait;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class StudentsUpdateValidationTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;
    use UserServiceTrait;

    /** @test */
    public function dni_must_be_alpha_numeric(): void
    {
        $user = User::factory()->create();

        $data = [
            'dni' => '4YRE832$%%'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');

        $this->assertDatabaseMissing('users', [
            'dni' => '4YRE832$%%'
        ]);
    }

    /** @test */
    public function dni_must_be_unique_in_database(): void
    {
        $dni = $this->generateDNIUnique();

        User::factory()->create(compact('dni'));

        $user = User::factory()->create();

        $data = compact('dni');

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');

        $data['dni'] = $user->dni;

        $this->patchJson($url, $data)->assertOk();

    }

    /** @test */
    public function dni_must_be_a_valid_dni_spain(): void
    {
        $dni = $this->generateDNIUnique() . '234jJDSGS';

        $user = User::factory()->create();

        $data = compact('dni');

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);
        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');
    }

    /** @test */
    public function first_name_must_be_at_least_3_characters(): void
    {

        $user = User::factory()->create();

        $data = [
            'first-name' => 'Ra'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_not_be_a_longer_than_25_characters(): void
    {
        $user = User::factory()->create();

        $data = [
            'first-name' => Str::random(26)
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_be_letters(): void
    {
        $user = User::factory()->create();

        $data = [
            'first-name' => 'Raul123'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_be_letters_with_optional_accents(): void
    {
        $user = User::factory()->create();

        $data = [
            'first-name' => 'Raúl_'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function last_name_must_be_at_least_3_characters(): void
    {
        $user = User::factory()->create();

        $data = [
            'last-name' => 'Mo'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_not_be_a_longer_than_25_characters(): void
    {
        $user = User::factory()->create();

        $data = [
            'last-name' => Str::random(26)
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_be_letters(): void
    {
        $user = User::factory()->create();

        $data = [
            'last-name' => 'Moheno 12345'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_be_letters_with_optional_accents(): void
    {
        $user = User::factory()->create();

        $data = [
            'last-name' => 'Álvarez_'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function phone_must_be_numeric(): void
    {
        $user = User::factory()->create();

        $data = [
            'phone' => '9123454%'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function phone_must_be_unique(): void
    {
        $phone = $this->getNumberPhoneSpain();

        User::factory()->create(compact('phone'));

        $user = User::factory()->create();

        $data = compact('phone');

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function phone_must_be_a_valid_number_spain(): void
    {
        $user = User::factory()->create();

        $data = [
            'phone' => '4998487566'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function email_must_be_a_email_valid(): void
    {
        $user = User::factory()->create();

        $data = [
            'email' => 'raul.moheno@.com'
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_a_unique(): void
    {
        $email = 'raul.albert.academia750@gmail.com';

        User::factory()->create(compact('email'));

        $user = User::factory()->create();

        $data = compact('email');

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function roles_must_be_array():void
    {
        $user = User::factory()->create();

        $data = [
            'roles' => $this->roleStudent->getRouteKey()
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);

        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles');
    }

    /** @test */
    public function roles_must_be_distinct():void
    {
        $user = User::factory()->create();

        $data = [
            'roles' => [
                $this->roleStudent->getRouteKey(),
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);
        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles.0')->assertJsonValidationErrorFor('roles.1');
    }

    /** @test */
    public function each_roles_must_be_exists_db():void
    {
        $user = User::factory()->create();

        $data = [
            'roles' => [
                $this->roleStudent->getRouteKey(),
                $this->getUUIDUnique(Role::class)
            ]
        ];

        $url = route('api.v1.users.update', ['user' => $user->getRouteKey()]);

        $response = $this->patchJson($url, $data);
        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles.1');
    }
}
