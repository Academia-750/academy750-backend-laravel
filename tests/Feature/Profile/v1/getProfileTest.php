<?php

namespace Tests\Feature\Profile\v1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class getProfileTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_fetch_data_simple_of_my_profile(): void
    {
        $url = route('api.v1.my-profile-auth');

        $response = $this->getJson($url);

        // asserts...
        $response->assertOk();

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $this->userAuth->getRouteKey(),
                'attributes' => [
                    'dni' => $this->userAuth->dni,
                    'first_name' => $this->userAuth->first_name,
                    'last_name' => $this->userAuth->last_name,
                    'phone' => $this->userAuth->phone,
                    'state_account' => 'enable',
                    'email' => $this->userAuth->email,
                    "email_verified_at" => ($this->userAuth->email_verified_at !== null) ? $this->userAuth->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($this->userAuth->last_session !== null) ? $this->userAuth->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $this->userAuth->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_fetch_data_of_my_profile_with_roles_and_permissions_relationships(): void
    {
        $role = Role::where('name', '=', 'admin')->first();
        $permissions = $role->permissions;

        $url = route('api.v1.my-profile-auth'). '?include=roles,roles-permissions';

        $response = $this->getJson($url);

        // asserts...
        $response->assertOk();

        $this->assertTrue($this->userAuth->hasRole('admin'));
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
