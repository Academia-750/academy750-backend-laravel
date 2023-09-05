<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class WorkspaceCreateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/workspace")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/workspace")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        $this->post("api/v1/workspace", [])->assertStatus(422);
        $this->post("api/v1/workspace", ['name' => ''])->assertStatus(422);
        $this->post("api/v1/workspace", ['name' => 'Others!'])->assertStatus(422);
    }
    /** @test */
    public function create_workspace_200(): void
    {
        $name = $this->faker->word();
        $data = $this->post("api/v1/workspace", ['name' => $name])->assertStatus(200)->json();

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['name'], $name);
        $this->assertEquals($data['result']['type'], 'material');
        $this->assertEquals($data['result']['tags'], '');
    }

    /** @test */
    public function name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->post("api/v1/workspace", ['name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }

}