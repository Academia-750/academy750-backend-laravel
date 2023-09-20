<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class RoleInfoTest extends TestCase
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
        $this->get("api/v1/role/{$this->role->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/role/{$this->role->id}")->assertStatus(403);
    }


    /** @test */
    public function role_not_found_404(): void
    {
        $this->get("api/v1/role/99")->assertStatus(404);
    }

    /** @test */
    public function get_role_200(): void
    {

        $data = $this->get("api/v1/role/{$this->role->id}")->assertStatus(200);

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['alias_name'], $this->role->alias_name);
        $this->assertEquals($data['result']['name'], $this->role->name);
        $this->assertEquals($data['result']['protected'], $this->role->protected);
        $this->assertEquals($data['result']['default_role'], $this->role->default_role);
    }

    /** @test */
    public function get_role_users_count_200(): void
    {

        User::factory()->withRole($this->role->name)->count(2)->create();

        $data = $this->get("api/v1/role/{$this->role->id}")->assertStatus(200);

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['users_count'], 2);

    }

    /** @test */
    public function with_permissions_200(): void
    {
        $permissions = Permission::query()->orderBy('name')->limit(3)->pluck('name')->toArray();
        $this->role->givePermissionTo($permissions);



        $data = $this->get("api/v1/role/{$this->role->id}")->assertStatus(200);

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals(count($data['result']['permissions']), 3);

        // Take one result and check the the data es return correctly
        $permission = Permission::find($data['result']['permissions'][0]['id']);
        $this->assertEquals($data['result']['permissions'][0]['name'], $permission->name);
        $this->assertEquals($data['result']['permissions'][0]['alias_name'], $permission->alias_name);
        $this->assertEquals($data['result']['permissions'][0]['category'], $permission->category);
        $this->assertNotNull($data['result']['permissions'][0]['created_at']);
        $this->assertNotNull($data['result']['permissions'][0]['updated_at']);


        // Compare the 3 permissions is the 3 ones we added
        $permissionsResponse = $this->map($data['result']['permissions'], 'name');
        $this->assertEquals($permissionsResponse, $permissions);
    }
}