<?php

namespace Tests\Feature\Students\v1\updateStudents;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class StudentsUpdateTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_update_only_first_name(): void
    {
        $user = User::factory()->create();

        $data = [
            'first-name' => 'Raúl Alberto'
        ];

        $url = route('api.v1.users.update', [ 'user' => $user->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'dni' => $user->dni,
            'first_name' => $data['first-name'],
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email,
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $data['first-name'],
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state,
                    'email' => $user->email,
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_only_last_name(): void
    {
        $user = User::factory()->create();

        $data = [
            'last-name' => 'Moheno Zavaleta'
        ];

        $url = route('api.v1.users.update', [ 'user' => $user->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'dni' => $user->dni,
            'first_name' => $user->first_name,
            'last_name' => $data['last-name'],
            'phone' => $user->phone,
            'email' => $user->email,
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $data['last-name'],
                    'phone' => $user->phone,
                    'state_account' => $user->state,
                    'email' => $user->email,
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_only_phone(): void
    {
        $user = User::factory()->create();

        $data = [
            'phone' => '912345678'
        ];

        $url = route('api.v1.users.update', [ 'user' => $user->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'dni' => $user->dni,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $data['phone'],
            'email' => $user->email,
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $data['phone'],
                    'state_account' => $user->state,
                    'email' => $user->email,
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_only_email(): void
    {
        $user = User::factory()->create();

        $data = [
            'email' => 'raul.moheno.webmaster@gmail.com'
        ];

        $url = route('api.v1.users.update', [ 'user' => $user->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'dni' => $user->dni,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $data['email'],
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state,
                    'email' => $data['email'],
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_only_roles(): void
    {
        $thisInstance = $this;

        $user = User::factory()->afterCreating(static function (User $user) use ($thisInstance){
            $user->assignRole($thisInstance->roleAdmin);
        })->create();

        $data = [
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.update', [ 'user' => $user->getRouteKey() ]) . '?include=roles';

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'dni' => $user->dni,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email,
        ]);

        $this->assertTrue(User::query()->find($user->getRouteKey())->hasRole('student'));

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state,
                    'email' => $user->email,
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                "type" => "roles",
                                "id" => $this->roleStudent->getRouteKey(),
                                "attributes" => [
                                    "roleName" => "student",
                                    "roleAliasName" => "Estudiante",
                                    "createdAt" => $this->roleStudent->created_at->format('Y-m-d h:m:s'),
                                ],
                                'relationships' => []
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}
