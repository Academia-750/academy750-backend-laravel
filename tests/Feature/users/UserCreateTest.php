<?php

namespace Tests\Feature;

use App\Core\Services\UserService;
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

class UserCreateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user; // Admin user

    private $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);


        $this->body = [
            "dni" => UserService::generateDNIUnique(),
            'email' => $this->faker()->unique()->safeEmail(),
            'first-name' => $this->faker->firstName(),
            'last-name' => $this->faker->lastName(),
            'phone' => UserService::getNumberPhoneSpain(),
        ];
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/users/create", [])->assertStatus(401);
    }


    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/users/create", [])->assertStatus(403);
    }


    /**
     * TODO: This is and endpoint of the phase 1.
     * Here we just added test cases for some changes in phase 2.
     * Is required to make the core of the test cases for this end point
     */

    /** @test */
    public function new_user_has_default_role_200(): void
    {

        $this->post("api/v1/users/create", $this->body)->assertStatus(201);

        $user = User::where('dni', $this->body['dni'])->first();
        $roles = $user->roles()->get()->toArray();

        $this->assertCount(1, $roles);

        $this->assertEquals($roles[0]['name'], Role::defaultRole()->name);
    }

    /** @test */
    public function modify_default_role_200(): void
    {

        $role = Role::factory()->defaultRole()->create();

        $this->post("api/v1/users/create", $this->body)->assertStatus(201);

        $user = User::where('dni', $this->body['dni'])->first();
        $roles = $user->roles()->get()->toArray();

        $this->assertCount(1, $roles);
        $this->assertEquals($roles[0]['name'], $role->name);
    }

    /** @test */
    public function default_role_not_set_500(): void
    {
        Role::where('default_role', true)->update(['default_role' => false]);
        $this->post("api/v1/users/create", $this->body)->assertStatus(500);
    }
}
