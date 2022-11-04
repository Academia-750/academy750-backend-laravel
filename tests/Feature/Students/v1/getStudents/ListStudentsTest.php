<?php

namespace Tests\Feature\Students\v1\getStudents;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class ListStudentsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_fetch_all_students(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'Adolfo'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Raul'
        ]);
        $user2->assignRole($this->roleStudent);

        $url = route('api.v1.users.index'). '?filter[role]=student&sort=first-name';

        $response = $this->getJson($url);

        //dump($response);

        // asserts...

        $response->assertOk();

        $response->assertJsonCount(2, 'data');

        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'users',
                    'id' => (string) $user1->getRouteKey(),
                    'attributes' => [
                        'dni' => $user1->dni,
                        'first_name' => $user1->first_name,
                        'last_name' => $user1->last_name,
                        'phone' => $user1->phone,
                        'state_account' => $user1->state,
                        'email' => $user1->email,
                        "email_verified_at" => ($user1->email_verified_at !== null) ? $user1->email_verified_at->format('Y-m-d h:m:s') : null ,
                        "last_session" => ($user1->last_session !== null) ? $user1->last_session->format('Y-m-d h:m:s') : null ,
                        "created_at" => $user1->created_at->format('Y-m-d h:m:s')
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
                ],
                [
                    'type' => 'users',
                    'id' => (string) $user2->getRouteKey(),
                    'attributes' => [
                        'dni' => $user2->dni,
                        'first_name' => $user2->first_name,
                        'last_name' => $user2->last_name,
                        'phone' => $user2->phone,
                        'state_account' => $user2->state,
                        'email' => $user2->email,
                        "email_verified_at" => ($user2->email_verified_at !== null) ? $user2->email_verified_at->format('Y-m-d h:m:s') : null ,
                        "last_session" => ($user2->last_session !== null) ? $user2->last_session->format('Y-m-d h:m:s') : null ,
                        "created_at" => $user2->created_at->format('Y-m-d h:m:s')
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
            ]
        ]);
    }

    /** @test */
    public function can_fetch_students_with_enable_account(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'Adolfo'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Carlos'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'first_name' => 'Raul',
            'state' => 'disable'
        ]);
        $user3->assignRole($this->roleStudent);

        $url = route('api.v1.users.index'). '?filter[role]=student&filter[state-account]=enable&sort=first-name';

        $response = $this->getJson($url);

        $response->assertOk();

        $response->assertJsonCount(2, 'data');

        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'users',
                    'id' => (string) $user1->getRouteKey(),
                    'attributes' => [
                        'dni' => $user1->dni,
                        'first_name' => $user1->first_name,
                        'last_name' => $user1->last_name,
                        'phone' => $user1->phone,
                        'state_account' => 'enable',
                        'email' => $user1->email,
                        "email_verified_at" => ($user1->email_verified_at !== null) ? $user1->email_verified_at->format('Y-m-d h:m:s') : null ,
                        "last_session" => ($user1->last_session !== null) ? $user1->last_session->format('Y-m-d h:m:s') : null ,
                        "created_at" => $user1->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    "type" => "roles",
                                    "id" => (string) $this->roleStudent->getRouteKey(),
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
                ],
                [
                    'type' => 'users',
                    'id' => (string) $user2->getRouteKey(),
                    'attributes' => [
                        'dni' => $user2->dni,
                        'first_name' => $user2->first_name,
                        'last_name' => $user2->last_name,
                        'phone' => $user2->phone,
                        'state_account' => 'enable',
                        'email' => $user2->email,
                        "email_verified_at" => ($user2->email_verified_at !== null) ? $user2->email_verified_at->format('Y-m-d h:m:s') : null ,
                        "last_session" => ($user2->last_session !== null) ? $user2->last_session->format('Y-m-d h:m:s') : null ,
                        "created_at" => $user2->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    "type" => "roles",
                                    "id" => (string) $this->roleStudent->getRouteKey(),
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
            ]
        ]);
    }

    /** @test */
    public function can_fetch_students_with_disable_account(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'Adolfo',
            'state' => 'disable'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Carlos'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'first_name' => 'Raul',
            'state' => 'disable'
        ]);
        $user3->assignRole($this->roleStudent);

        $url = route('api.v1.users.index'). '?filter[role]=student&filter[state-account]=disable&sort=first-name';

        $response = $this->getJson($url);

        $response->assertOk();

        $response->assertJsonCount(2, 'data');

        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'users',
                    'id' => (string) $user1->getRouteKey(),
                    'attributes' => [
                        'dni' => $user1->dni,
                        'first_name' => $user1->first_name,
                        'last_name' => $user1->last_name,
                        'phone' => $user1->phone,
                        'state_account' => 'disable',
                        'email' => $user1->email,
                        "email_verified_at" => ($user1->email_verified_at !== null) ? $user1->email_verified_at->format('Y-m-d h:m:s') : null ,
                        "last_session" => ($user1->last_session !== null) ? $user1->last_session->format('Y-m-d h:m:s') : null ,
                        "created_at" => $user1->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    "type" => "roles",
                                    "id" => (string) $this->roleStudent->getRouteKey(),
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
                ],
                [
                    'type' => 'users',
                    'id' => (string) $user3->getRouteKey(),
                    'attributes' => [
                        'dni' => $user3->dni,
                        'first_name' => $user3->first_name,
                        'last_name' => $user3->last_name,
                        'phone' => $user3->phone,
                        'state_account' => 'disable',
                        'email' => $user3->email,
                        "email_verified_at" => ($user3->email_verified_at !== null) ? $user3->email_verified_at->format('Y-m-d h:m:s') : null ,
                        "last_session" => ($user3->last_session !== null) ? $user3->last_session->format('Y-m-d h:m:s') : null ,
                        "created_at" => $user3->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    "type" => "roles",
                                    "id" => (string) $this->roleStudent->getRouteKey(),
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
            ]
        ]);
    }
}
