<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class MaterialCreateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $workspace;



    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->workspace = Workspace::factory()->create();

    }

    /** @test */
    public function not_workspace_id_405(): void
    {
        $this->post("api/v1/workspace/add")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/workspace/{$this->workspace->id}/add")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/workspace/{$this->workspace->id}/add")->assertStatus(403);
    }


    /** @test */
    public function workspace_not_found(): void
    {
        $data = [
            'name' => $this->faker->word(),
            'type' => Material::factory()->randomType()
        ];
        $this->post("api/v1/workspace/99/add", $data)->assertStatus(404);
    }


    /** @test */
    public function wrong_params_422(): void
    {
        $this->post("api/v1/workspace/{$this->workspace->id}/add", [])->assertStatus(422);
        // Name
        $this->post("api/v1/workspace/{$this->workspace->id}/add", ['type' => 'material'])->assertStatus(422);
        $this->post("api/v1/workspace/{$this->workspace->id}/add", ['name' => '', 'type' => 'material'])->assertStatus(422);
        $this->post("api/v1/workspace/{$this->workspace->id}/add", ['name' => 'Others!', 'type' => 'material'])->assertStatus(422);
        // Type
        $this->post("api/v1/workspace/{$this->workspace->id}/add", ['name' => 'CorrectName'])->assertStatus(422);
        $this->post("api/v1/workspace/{$this->workspace->id}/add", ['name' => 'CorrectName', 'type' => 'wrong_type'])->assertStatus(422);

    }
    /** @test */
    public function create_a_file_200(): void
    {
        $name = $this->faker->word();
        $type = Material::factory()->randomType();

        $data = $this->post("api/v1/workspace/{$this->workspace->id}/add", ['name' => $name, 'type' => $type])->assertStatus(200)->json();

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['name'], $name);
        $this->assertEquals($data['result']['type'], $type);
        $this->assertEquals($data['result']['tags'], '');
        $this->assertEquals($data['result']['url'], '');
        $this->assertEquals($data['result']['workspace_id'], $this->workspace->id);
    }

    /** @test */
    public function name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->post("api/v1/workspace/{$this->workspace->id}/add", ['name' => $valid_string, 'type' => Material::factory()->randomType()])->assertStatus(200);
        }, $this->valid_string_input);
    }

}