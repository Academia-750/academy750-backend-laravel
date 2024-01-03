<?php

namespace Tests\Feature;

use App\Core\Resources\Storage\Services\DummyStorage;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;



class MaterialEditTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $material;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->material = Material::factory()->create();
    }


    /** @test */
    public function not_logged_401(): void
    {

        Auth::logout();
        $this->put("api/v1/material/{$this->material->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->put("api/v1/material/{$this->material->id}")->assertStatus(403);
    }


    /** @test */
    public function material_not_found_404(): void
    {

        $this->put("api/v1/material/99", [])->assertStatus(404);
    }


    /** @test */
    public function wrong_params_422(): void
    {
        // Tags
        $this->put("api/v1/material/{$this->material->id}", ['tags' => 'not-array'])->assertStatus(422);
        $this->put("api/v1/material/{$this->material->id}", ['tags' => ['normal', '!!']])->assertStatus(422);
        // Name
        $this->put("api/v1/material/{$this->material->id}", ['name' => []])->assertStatus(422);
        $this->put("api/v1/material/{$this->material->id}", ['name' => '!!'])->assertStatus(422);
        // URL
        $this->put("api/v1/material/{$this->material->id}", ['url' => 'not_url'])->assertStatus(422);
        // Watermark
        $this->put("api/v1/material/{$this->material->id}", ['watermark' => 'no_boolean'])->assertStatus(422);

    }
    /** @test */
    public function update_name_200(): void
    {
        $name = $this->faker->word();

        $data = $this->put("api/v1/material/{$this->material->id}", ['name' => $name])->assertStatus(200)->json();

        $this->assertEquals($data['result']['name'], $name);
    }

    /** @test */
    public function update_all_fields_200(): void
    {
        $body = [
            'name' => $this->faker->word(),
            'url' => $this->faker->url(),
            'tags' => $this->faker->words(),
            'watermark' => 1
        ];

        $data = $this->put("api/v1/material/{$this->material->id}", $body)->assertStatus(200)->json();

        $this->assertEquals($data['result']['name'], $body['name']);
        $this->assertEquals($data['result']['type'], $this->material->type);
        $this->assertEquals($data['result']['tags'], join(',', $body['tags']));
        $this->assertEquals($data['result']['url'], $body['url']);
        $this->assertEquals($data['result']['watermark'], $body['watermark']);
    }


    /** @test */
    public function update_tag_200(): void
    {
        // Single Tag
        $tag = $this->faker->word();
        $data = $this->put("api/v1/material/{$this->material->id}", ['tags' => [$tag]])->assertStatus(200)->json();
        $this->assertEquals($data['result']['tags'], $tag);
        // Multiple Tags
        $tags = $this->faker->words();
        $data = $this->put("api/v1/material/{$this->material->id}", ['tags' => $tags])->assertStatus(200)->json();
        $this->assertEquals($data['result']['tags'], join(',', $tags));
    }

    /** @test */
    public function update_watermkar_200(): void
    {
        $this->assertEquals($this->material->watermark, false);
        // Add Watermark
        $data = $this->put("api/v1/material/{$this->material->id}", ['watermark' => true])->assertStatus(200)->json();
        $this->assertEquals($data['result']['watermark'], true);
        // Remove watermark
        $data = $this->put("api/v1/material/{$this->material->id}", ['watermark' => false])->assertStatus(200)->json();
        $this->assertEquals($data['result']['watermark'], false);
    }

    /** @test */
    public function update_url_200(): void
    {

        $storageSpy = $this->spy(DummyStorage::class)->makePartial();

        $url = $this->faker->url();

        $data = $this->put("api/v1/material/{$this->material->id}", ['url' => $url])
            ->assertStatus(200)
            ->json();

        $this->assertEquals($data['result']['url'], $url);
        $storageSpy->shouldNotHaveReceived('deleteFile'); // because original URL is empty


    }

    /** @test */
    public function override_url_200(): void
    {

        $storageSpy = $this->spy(DummyStorage::class)->makePartial();

        $material = Material::factory()->state(['type' => 'material', 'url' => $this->faker()->url()])->create();
        $url = $this->faker()->url();
        $this->put("api/v1/material/{$material->id}", ['url' => $url])
            ->assertStatus(200)
            ->json();
        $storageSpy->shouldHaveReceived('deleteFile')->once()->with(Mockery::on(function ($argument) use ($material) {
            return $argument->id === $material->id;
        }));

    }

    /** @test */
    public function error_from_storage_424(): void
    {

        $storageMock = $this->mock(DummyStorage::class)->makePartial();
        $storageMock->shouldReceive('deleteFile')->andReturn(['status' => 401, 'error' => 'Not authorized']);

        $material = Material::factory()->state(['type' => 'material', 'url' => $this->faker()->url()])->create();
        $url = $this->faker()->url();
        $this->put("api/v1/material/{$material->id}", ['url' => $url])
            ->assertStatus(424)
            ->json();
    }

    /** @test */
    public function name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->put("api/v1/material/{$this->material->id}", ['name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }

    /** @test */
    public function tag_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->put("api/v1/material/{$this->material->id}", ['tag' => [$valid_string]])->assertStatus(200);
        }, $this->valid_string_input);
    }

}
