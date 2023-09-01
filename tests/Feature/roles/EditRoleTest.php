<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class EditRoleTest extends TestCase
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
        $this->put("api/v1/role/{$this->role->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->put("api/v1/role/{$this->role->id}")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {

        $this->put("api/v1/role/{$this->role->id}", ['name' => "s"])->assertStatus(422); // Too short
        $this->put("api/v1/role/{$this->role->id}", ['name' => "! Bad chars"])->assertStatus(422); // Bad Chars

        $this->put("api/v1/role/{$this->role->id}", ['default_role' => 'wrong_format'])->assertStatus(422); // Wrong format
        $this->put("api/v1/role/{$this->role->id}", ['default_role' => false])->assertStatus(422); // Can not be false

    }
    /** @test */
    public function role_not_found_404(): void
    {
        $this->put("api/v1/role/99", [])->assertStatus(404);
    }



    /** @test */
    public function empty_call_200(): void
    {
        $this->put("api/v1/role/{$this->role->id}", [])->assertStatus(200);
    }

    /** @test */
    public function update_role_name_200(): void
    {

        $data = $this->put("api/v1/role/{$this->role->id}", ['name' => "New Role"])->assertStatus(200);

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['alias_name'], "New Role");
        $this->assertEquals($data['result']['name'], "new_role");
    }

    /** @test */
    public function create_a_role_parse_name_200(): void
    {

        // Spaces changes to  -
        $data = $this->put("api/v1/role/{$this->role->id}", ['name' => 'My Name'])->assertStatus(200);
        $this->assertEquals($data['result']['name'], 'my_name');

        // To lowercase
        $data = $this->put("api/v1/role/{$this->role->id}", ['name' => 'CaPiTal'])->assertStatus(200);
        $this->assertEquals($data['result']['name'], 'capital');

        // Spanish Characters
        $data = $this->put("api/v1/role/{$this->role->id}", ['name' => 'ÁéÍóÚñ'])->assertStatus(200);
        $this->assertEquals($data['result']['name'], 'aeioun');
    }

    /** @test */
    public function update_role_name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->put("api/v1/role/{$this->role->id}", ['name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }

    /** @test */
    public function set_default_role_200(): void
    {
        // By default the seeded role is the `default_role` and our new created role is not
        $defaultRole = Role::where('default_role', true)->first();
        $this->assertNotNull($defaultRole);
        $this->assertEquals($this->role->default_role, false);

        // Update
        $data = $this->put("api/v1/role/{$this->role->id}", ['default_role' => true])->assertStatus(200);
        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['default_role'], true);

        $oldDefault = Role::where('id', $defaultRole->id)->first();
        $this->assertEquals($oldDefault->default_role, false);

        $this->assertEquals(Role::where('default_role', true)->count(), 1);

    }
}