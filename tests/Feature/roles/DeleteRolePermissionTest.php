<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;


class DeleteRolePermissionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $body;

    private $role;

    private $permission; // Is an array, because we shuffle it first


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->role = Role::factory()->create(['name' => 'My Role', 'alias_name' => 'my_role']);
        $permissions = Permission::limit(20)->get()->toArray();
        shuffle($permissions);

        $this->permission = $permissions[0];
        $this->role->givePermissionTo($this->permission['name']);
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => $this->permission['id']])->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => $this->permission['id']])->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        $this->delete("api/v1/role/{$this->role->id}/permission", [])->assertStatus(422); // No data
        $this->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => 99])->assertStatus(422); // wrong type
        $this->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => 'asdasd'])->assertStatus(422); // Not UUID
    }
    /** @test */
    public function role_not_found_404(): void
    {
        $this->delete("api/v1/role/99/permission", ['permission_id' => $this->permission['id']])->assertStatus(404);
    }

    /** @test */
    public function permission_not_found_403(): void
    {
        $this->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => Str::uuid()->toString()])->assertStatus(403);
    }

    /** @test */
    public function permission_is_not_linked_409(): void
    {
        $this->role->revokePermissionTo($this->permission['name']);

        $this->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => $this->permission['id']])->assertStatus(409);
    }


    /** @test */
    public function remove_permission_to_role_200(): void
    {
        $this->delete("api/v1/role/{$this->role->id}/permission", ['permission_id' => $this->permission['id']])->assertStatus(200);

        $updated = Role::find($this->role->id);
        $this->assertEquals($updated->hasPermissionTo($this->permission['name']), false);
    }

}