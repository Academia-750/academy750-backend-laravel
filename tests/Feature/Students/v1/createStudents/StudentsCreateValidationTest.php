<?php

namespace Tests\Feature\Students\v1\createStudents;

use App\Core\Services\UserServiceTrait;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class StudentsCreateValidationTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;
    use UserServiceTrait;

    /** @test */
    public function dni_is_required(): void
    {
        $data = [
            'first-name' => 'Raúl',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');

        $this->assertDatabaseMissing('users', [
            'first_name' => $data['first-name'],
            'last_name' => $data['last-name'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);
    }

    /** @test */
    public function dni_must_be_alpha_numeric(): void
    {
        $data = [
            'dni' => '4YRE832$%%',
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');

        $this->assertDatabaseMissing('users', [
            'first_name' => $data['first-name'],
            'last_name' => $data['last-name'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);
    }

    /** @test */
    public function dni_must_be_unique_in_database(): void
    {
        $dni = $this->generateDNIUnique();

        User::factory()->create(compact('dni'));

        $data = [
            'dni' => $dni,
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');

        $this->assertDatabaseMissing('users', [
            'first_name' => $data['first-name'],
            'last_name' => $data['last-name'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);
    }

    /** @test */
    public function dni_must_be_a_valid_dni_spain(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique() . '32dfdgsgsdgs',
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('dni');

        $this->assertDatabaseMissing('users', [
            'first_name' => $data['first-name'],
            'last_name' => $data['last-name'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);
    }

    /** @test */
    public function first_name_is_required(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            //'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_be_at_least_3_characters(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Ra',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_not_be_a_longer_than_25_characters(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => Str::random(26),
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_be_letters(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Raul123',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function first_name_must_be_letters_with_optional_accents(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'RaúlA_',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('first-name');
    }

    /** @test */
    public function last_name_is_required(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            //'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_be_at_least_3_characters(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Raúl',
            'last-name' => 'Mo',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_not_be_a_longer_than_25_characters(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Raúl',
            'last-name' => Str::random(26),
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_be_letters(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Raúl',
            'last-name' => 'Moh343$',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function last_name_must_be_letters_with_optional_accents(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Raúl',
            'last-name' => 'Álvaro_',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('last-name');
    }

    /** @test */
    public function phone_is_required(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            //'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function phone_must_be_numeric(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '9321657BA',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function phone_must_be_unique(): void
    {
        $phone = '932165798';

        User::factory()->create(compact('phone'));

        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => $phone,
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function phone_must_be_a_valid_number_spain(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '9984875616',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');

        $data['phone'] = '469686001';

        $response = $this->postJson($url, $data);

        $response->assertStatus(422)->assertJsonValidationErrorFor('phone');
    }

    /** @test */
    public function email_is_required(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            //'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_a_email_valid(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_a_unique(): void
    {
        $email = 'raul.albert.academia750@gmail.com';

        User::factory()->create(compact('email'));

        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => $email,
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertStatus(422)->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function roles_is_required():void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.moheno.webmaster@gmail.com',
            'roles' => []
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);
        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles');

        $response->assertJsonFragment([
            "errors" => [
                "roles" => [
                    "El campo Roles es requerido."
                ]
            ]
        ]);
    }

    /** @test */
    public function roles_must_be_array():void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.moheno.webmaster@gmail.com',
            'roles' => "student"
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);
        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles');

        $response->assertJsonFragment([
            "errors" => [
                "roles" => [
                    "El campo Roles debe ser un arreglo."
                ]
            ]
        ]);
    }

    /** @test */
    public function roles_must_be_distinct():void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.moheno.webmaster@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey(),
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);
        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles.0')->assertJsonValidationErrorFor('roles.1');
    }

    /** @test */
    public function each_roles_must_be_exists_db():void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.moheno.webmaster@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey(),
                $this->getUUIDUnique(Role::class)
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data);
        // asserts...
        $response->assertStatus(422)->assertJsonValidationErrorFor('roles.1');
    }
}
