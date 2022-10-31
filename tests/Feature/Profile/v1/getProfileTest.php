<?php

namespace Tests\Feature\Profile\v1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class getProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_data_simple_of_my_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $url = route('api.v1.my-profile-auth');

        $response = $this->getJson($url);

        // asserts...
        $response->assertOk();

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state === 1 ? 'enable': 'disabled',
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
    public function can_fetch_data_of_my_profile_with_roles_and_permissions_relationships(): void
    {
        $user = User::factory()->create();
        $role = Role::where('name', '=', 'admin')->first();
        $permissions = $role->permissions;

        $user->assignRole('admin');

        Sanctum::actingAs($user);

        $url = route('api.v1.my-profile-auth'). '?include=roles,roles-permissions';

        $response = $this->getJson($url);

        // asserts...
        $response->assertOk();

        $this->assertTrue($user->hasRole('admin'));
        $this->assertNotNull($role);

        $response->assertSee($role->name);
        $response->assertSee($role->permissions->first()->name);

        $response->assertJsonStructure([
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'attributes',
                                'relationships' => [
                                    'permissions' => [
                                        'data'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertJsonCount($permissions->count(), 'data.relationships.roles.data.0.relationships.permissions.data');
    }
}
