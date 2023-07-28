<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetTagTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $tags;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->tags = Tag::factory()->type('material')->count(4)->create();

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/material/tag")->assertStatus(401);
    }



    /** @test */
    public function get_tag_list_200(): void
    {
        $data = $this->get("api/v1/material/tag")->assertStatus(200)->json();

        $this->assertEquals(count($data['results']), 4);
        $this->assertEquals($data['total'], 4);
    }

    /** @test */
    public function get_tag_list_limit_200(): void
    {
        $data = $this->get("api/v1/material/tag?" . Arr::query(['limit' => 2]))->assertStatus(200)->json();
        $this->assertEquals(count($data['results']), 2);
        $this->assertEquals($data['total'], 4);

        $data1 = $this->get("api/v1/material/tag?" . Arr::query(['limit' => 1]))->assertStatus(200)->json();
        $this->assertEquals(count($data1['results']), 1);
        $this->assertEquals($data['total'], 4);
    }

    /** @test */
    public function get_tag_content_200(): void
    {
        $data = $this->get("api/v1/material/tag?" . Arr::query(['content' => substr($this->tags[0]->name, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($data['results'][0]['name'], $this->tags[0]->name);
        $this->assertEquals($data['total'], 1);


        $data = $this->get("api/v1/material/tag?" . Arr::query(['content' => substr($this->tags[1]->name, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($data['results'][0]['name'], $this->tags[1]->name);
        $this->assertEquals($data['total'], 1);

    }

    /** @test */
    public function no_data_200(): void
    {
        $data = $this->get("api/v1/material/tag?" . Arr::query(['content' => 'no_data']))->assertStatus(200)->json();
        $this->assertEmpty($data['results']);
        $this->assertEquals($data['total'], 0);
    }


    /** @test */
    public function name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->get("api/v1/material/tag", ['name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);

    }

}