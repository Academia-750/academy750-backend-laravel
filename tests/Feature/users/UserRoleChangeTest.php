<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserRoleChanpostest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user; // Admin user
    private $updateUser;
    private $updateRole;

    private $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->updateUser = User::factory()->student()->create();
        $this->updateRole = Role::factory()->create();

        $this->body = ['user_id' => $this->updateUser->uuid, 'role_id' => $this->updateRole->id];
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/users/role", $this->body)->assertStatus(401);
    }


    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/users/role", [])->assertStatus(403);

    }


    /** @test */
    public function wrong_params_422(): void
    {
        $this->post("api/v1/users/role?", [])->assertStatus(422); // No data
        $this->post("api/v1/users/role?", ['user_id' => $this->body['user_id']])->assertStatus(422); // No role id
        $this->post("api/v1/users/role?", ['user_id' => $this->body['user_id'], 'role_id' => 123])->assertStatus(422); // wrong format
        $this->post("api/v1/users/role?", ['role_id' => $this->body['role_id']])->assertStatus(422); // No user id
        $this->post("api/v1/users/role?", ['user_id' => 123, 'role_id' => $this->body['role_id']])->assertStatus(422); // wrong format
    }



    /** @test */
    public function user_not_found_404(): void
    {
        $this->post("api/v1/users/role", ['user_id' => $this->faker()->uuid(), 'role_id' => $this->body['role_id']])->assertStatus(404);
    }

    /** @test */
    public function role_not_found_404(): void
    {
        $this->post("api/v1/users/role", ['role_id' => $this->faker()->uuid(), 'user_id' => $this->body['user_id']])->assertStatus(404);
    }

    /** @test */
    public function can_not_set_admin_role_409(): void
    {
        $admin = Role::where('name', 'admin')->first();
        $this->post("api/v1/users/role", ['role_id' => $admin->id, 'user_id' => $this->body['user_id']])->assertStatus(409);
    }

    /** @test */
    public function can_not_update_admin_users_403(): void
    {
        $admin = User::factory()->admin()->create();
        $this->post("api/v1/users/role", ['user_id' => $admin->uuid, 'role_id' => $this->body['role_id']])->assertStatus(403);
    }

    /** @test */
    public function assign_role_200(): void
    {
        $this->assertTrue($this->updateUser->hasRole('student')); // Default role

        $this->post("api/v1/users/role", $this->body)->assertStatus(200);

        $updated = User::find($this->updateUser->id);

        $this->assertTrue($updated->hasRole($this->updateRole->name));
        $this->assertFalse($updated->hasRole('student'));
    }

    /** @test */
    public function assign_role_removes_session_200(): void
    {
        $this->updateUser->createToken($this->updateUser);

        $this->assertNotNull($this->updateUser->tokens()->first());

        $this->post("api/v1/users/role", $this->body)->assertStatus(200);

        $this->assertNull($this->updateUser->tokens()->first());
    }


}