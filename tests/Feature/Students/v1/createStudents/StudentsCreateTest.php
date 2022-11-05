<?php

namespace Tests\Feature\Students\v1\createStudents;

use App\Core\Services\UserServiceTrait;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class StudentsCreateTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;
    use UserServiceTrait;

    /** @test */
    public function can_create_a_student(): void
    {
        $data = [
            'dni' => $this->generateDNIUnique(),
            'first-name' => 'Alberto',
            'last-name' => 'Moheno',
            'phone' => '932165478',
            'email' => 'raul.albert.academia750@gmail.com',
            'roles' => [
                $this->roleStudent->getRouteKey()
            ]
        ];

        $url = route('api.v1.users.create');

        $response = $this->postJson($url, $data)->dump();

        // asserts...

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'first_name' => $data['first-name'],
            'last_name' => $data['last-name'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);

        $this->assertNotNull($user = User::query()->where('first_name', '=', 'Alberto')->first());

        $this->assertTrue(User::query()->where('first_name', '=', 'Alberto')->first()->hasRole('student'));

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
                'relationships' => []
            ]
        ]);
    }
}
