<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;


class DeleteRoleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $body;

    private $role;



    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->role = Role::factory()->create(['name' => 'My Role', 'alias_name' => 'my_role']);

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/role/{$this->role->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/role/{$this->role->id}")->assertStatus(403);
    }


    /** @test */
    public function role_not_found_404(): void
    {
        $this->delete("api/v1/role/99")->assertStatus(404);
    }

    /** @test */
    public function role_protected_403(): void
    {
        $this->role->protected = 1;
        $this->role->save();

        $this->put("api/v1/role/{$this->role->id}", [])->assertStatus(403);
    }

    /** @test */
    public function default_role_409(): void
    {
        $this->role->default_role = true;
        $this->role->save();

        $this->put("api/v1/role/{$this->role->id}", [])->assertStatus(403);
    }

    /** @test */
    public function delete_role_200(): void
    {

        $this->delete("api/v1/role/{$this->role->id}")->assertStatus(200);

        $this->assertEquals(Role::where('id', $this->role->id)->count(), 0);
    }

    /** @test */
    public function delete_remove_association_with_permissions_200(): void
    {
        $permissions = Permission::query()->orderBy('name')->limit(3)->pluck('name')->toArray();
        $this->role->givePermissionTo($permissions);
        $this->assertEquals(DB::table('role_has_permissions')->where('role_id', $this->role->id)->count(), 3);


        $this->delete("api/v1/role/{$this->role->id}")->assertStatus(200);

        $this->assertEquals(DB::table('role_has_permissions')->where('role_id', $this->role->id)->count(), 0);
    }

    /** @test */
    public function delete_remove_association_with_users_200(): void
    {
        User::factory()->withRole($this->role->name)->count(3)->create();
        $this->assertEquals(DB::table('model_has_roles')->where('role_id', $this->role->id)->count(), 3);

        $this->delete("api/v1/role/{$this->role->id}")->assertStatus(200);

        $this->assertEquals(DB::table('model_has_roles')->where('role_id', $this->role->id)->count(), 0);
    }

}