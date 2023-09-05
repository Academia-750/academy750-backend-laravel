<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class RoleCreateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

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
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
        ];

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/role")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/role")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {

        $this->post("api/v1/role", [])->assertStatus(422); // No data
        $this->post("api/v1/role", ['name' => "s"])->assertStatus(422); // Too short
        $this->post("api/v1/role", ['name' => "! Bad chars"])->assertStatus(422); // Bad Chars

    }



    /** @test */
    public function create_a_role_200(): void
    {

        $data = $this->post("api/v1/role", $this->body)->assertStatus(200);


        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['alias_name'], $this->body['name']);
    }

    /** @test */
    public function create_a_role_parse_name200(): void
    {

        // Spaces changes to  -
        $data = $this->post("api/v1/role", ['name' => 'My Name'])->assertStatus(200);
        $this->assertEquals($data['result']['name'], 'my_name');

        // To lowercase
        $data = $this->post("api/v1/role", ['name' => 'CaPiTal'])->assertStatus(200);
        $this->assertEquals($data['result']['name'], 'capital');

        // Spanish Characters
        $data = $this->post("api/v1/role", ['name' => 'ÁéÍóÚñ'])->assertStatus(200);
        $this->assertEquals($data['result']['name'], 'aeioun');
    }

    /** @test */
    public function role_name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->post("api/v1/role", [...$this->body, 'name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }
}