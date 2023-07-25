<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class GetMaterialListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $materials;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        /**
         * 2 Materials in each workspace
         */
        $this->materials = Material::factory()->
            count(4)
            ->state(
                new Sequence(
                    ['workspace_id' => Workspace::factory()->create()->id],
                    ['workspace_id' => Workspace::factory()->create()->id]
                )
            )
            ->sequence(fn($sequence) => ['updated_at' => now()->add($sequence->index * 10, 'seconds')])
            ->withTags()
            ->withUrl()
            ->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/material/list")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/material/list")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        array_map(function ($input) {
            $this->get("api/v1/material/list?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);
        $this->get("api/v1/material/list?" . Arr::query(['type' => "not_valid"]))->assertStatus(422);
        $this->get("api/v1/material/list?" . Arr::query(['tags' => "no_array"]))->assertStatus(422);
        $this->get("api/v1/material/list?" . Arr::query(['workspace' => "not_an_id"]))->assertStatus(422);
    }


    /** @test */
    public function get_material_list_200(): void
    {
        $response = $this->get("api/v1/material/list", )->assertStatus(200)->json();

        $this->assertEquals(count($response['results']), 4);
        $this->assertEquals($response['total'], 4);
    }

    /** @test */
    public function get_material_info_200(): void
    {
        $response = $this->get("api/v1/material/list?" . Arr::query(['content' => $this->materials[0]->name]), )->assertStatus(200)->json();

        $this->assertEquals($response['total'], 1);
        $this->assertEquals($response['results'][0]['id'], $this->materials[0]->id);
        $this->assertEquals($response['results'][0]['name'], $this->materials[0]->name);
        $this->assertEquals($response['results'][0]['type'], $this->materials[0]->type);
        $this->assertEquals($response['results'][0]['url'], $this->materials[0]->url);
        $this->assertEquals($response['results'][0]['tags'], $this->materials[0]->tags);
        $this->assertEquals($response['results'][0]['workspace_id'], $this->materials[0]->workspace_id);

    }

    /** @test */
    public function filter_by_tags_200(): void
    {
        $tags = explode(',', $this->materials[0]->tags);

        $response = $this->get("api/v1/material/list?" . Arr::query(['tags' => [$tags[0]]]), )->assertStatus(200)->json();

        $this->assertEquals($response['total'], 1);
        $this->assertEquals($response['results'][0]['id'], $this->materials[0]->id);

        $tags2 = explode(',', $this->materials[1]->tags);

        $response = $this->get("api/v1/material/list?" . Arr::query(['tags' => [$tags[0], $tags2[0]]]))->assertStatus(200)->json();
        $this->assertEquals($response['total'], 2);

        $list2 = [$this->materials[0]->tags, $this->materials[1]->tags];
        sort($list2);

        $tagsResult = $this->map($response['results'], 'tags');
        sort($tagsResult);

        $this->assertEquals($tagsResult, $list2);
    }


    /** @test */
    public function filter_by_type_200(): void
    {
        $response = $this->get("api/v1/material/list?" . Arr::query(['type' => $this->materials[3]->type]))->assertStatus(200)->json();
        $types = $this->map($response['results'], 'type');

        $this->assertEquals(array_unique($types), [$this->materials[3]->type]);
    }


    /** @test */
    public function filter_by_tag(): void
    {
        $response = $this->get("api/v1/material/list?" . Arr::query(['workspace' => $this->materials[0]->workspace_id]), )->assertStatus(200)->json();

        $this->assertEquals($response['total'], 2);
        $this->assertEquals($response['results'][0]['workspace_id'], $this->materials[0]->workspace_id);
        $this->assertEquals($response['results'][1]['workspace_id'], $this->materials[0]->workspace_id);

        $response = $this->get("api/v1/material/list?" . Arr::query(['workspace' => $this->materials[1]->workspace_id]), )->assertStatus(200)->json();



        $this->assertEquals($response['total'], 2);
        $this->assertEquals($response['results'][0]['workspace_id'], $this->materials[1]->workspace_id);
        $this->assertEquals($response['results'][1]['workspace_id'], $this->materials[1]->workspace_id);

    }



    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/material/list?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->json();
        $data1 = $this->get("api/v1/material/list?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->json();
        $data2 = $this->get("api/v1/material/list?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->json();
        $data3 = $this->get("api/v1/material/list?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->json();

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
        $dataResponse = $this->get("api/v1/material/list?")->assertStatus(200)->json();
        $createdAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        rsort($sorted);

        dump($dataResponse);

        $this->assertEquals($createdAt, $sorted);
    }


    /** @test */
    public function order_by_200(): void
    {
        array_map(function ($orderBy) {
            $dataResponse = $this->get("api/v1/material/list?" . Arr::query(['orderBy' => $orderBy, 'order' => -1]))->assertStatus(200)->json();

            $attributeList = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributeList;
            rsort($sorted);

            $this->assertEquals($attributeList, $sorted);
        }, ['type', 'name']);
    }

    /** @test */
    public function material_default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/material/list?" . Arr::query(['order' => 1]))->assertStatus(200)->json();
        $updatedAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }
}