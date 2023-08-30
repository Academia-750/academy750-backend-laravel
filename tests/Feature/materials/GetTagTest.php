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



    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/material/tag?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->json();
        $data1 = $this->get("api/v1/material/tag?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->json();
        $data2 = $this->get("api/v1/material/tag?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->json();
        $data3 = $this->get("api/v1/material/tag?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->json();

        // Verify that each page we return a different object
        $ids = [$data1['results'][0]['id'], $data2['results'][0]['id'], $data3['results'][0]['id'], $data['results'][0]['id']];

        $this->assertEquals(count(array_unique($ids)), 4);
        $this->assertEquals(count($data['results']), 1);
        $this->assertEquals($data['total'], 4);
        $this->assertEquals($data1['total'], 4);
    }



    /** @test */

    public function default_order_200(): void
    {
        $dataResponse = $this->get("api/v1/material/tag?")->assertStatus(200)->json();
        $createdAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        rsort($sorted);

        $this->assertEquals($createdAt, $sorted);
    }


    /** @test */
    public function order_by_200(): void
    {
        array_map(function ($orderBy) {
            $dataResponse = $this->get("api/v1/material/tag?" . Arr::query(['orderBy' => $orderBy, 'order' => -1]))->assertStatus(200)->json();

            $attributeList = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributeList;
            rsort($sorted);

            $this->assertEquals($attributeList, $sorted);
        }, ['name', 'created_at', 'updated_at']);
    }

    /** @test */
    public function material_default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/material/tag?" . Arr::query(['order' => 1]))->assertStatus(200)->json();
        $updatedAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }

}