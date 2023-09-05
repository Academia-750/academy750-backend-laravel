<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class WorkspaceListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $workspaces;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->workspaces = Workspace::factory()->count(4)->sequence(
            ['updated_at' => now()->addSeconds(2)],
            ['updated_at' => now()->addSeconds(5)],
            ['updated_at' => now()->addSeconds(8)],
            ['updated_at' => now()->addSeconds(10)],
        )->type('material')->create();
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/workspace/list")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/workspace/list")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        array_map(function ($input) {
            $this->get("api/v1/workspace/list?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);
    }

    /** @test */
    public function get_workspace_list_200(): void
    {
        $data = $this->get("api/v1/workspace/list")->assertStatus(200)->json();

        $this->assertEquals(count($data['results']), 4);
        $this->assertEquals($data['total'], 4);
    }


    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/workspace/list?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->json();
        $data1 = $this->get("api/v1/workspace/list?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->json();
        $data2 = $this->get("api/v1/workspace/list?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->json();
        $data3 = $this->get("api/v1/workspace/list?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->json();

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
        $dataResponse = $this->get("api/v1/workspace/list?")->assertStatus(200)->json();
        $createdAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        rsort($sorted);

        $this->assertEquals($createdAt, $sorted);
    }

    /** @test */
    public function default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/workspace/list?" . Arr::query(['order' => 1]))->assertStatus(200)->json();
        $createdAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        sort($sorted);

        $this->assertEquals($createdAt, $sorted);
    }

    /** @test */
    public function order_by_name_200(): void
    {
        $dataResponse = $this->get("api/v1/workspace/list?" . Arr::query(['orderBy' => 'name', 'order' => 1]))->assertStatus(200)->json();

        $attributeList = array_map(function ($data) {
            return $data['name'];
        }, $dataResponse['results'], );

        $sorted = $attributeList;
        sort($sorted);

        $this->assertEquals($attributeList, $sorted);
    }

    /** @test */
    public function order_by_material_count_200(): void
    {

        Material::factory()->count(3)->create(['workspace_id' => $this->workspaces[0]->id]);
        Material::factory()->state(['workspace_id' => $this->workspaces[1]->id])->count(2)->create();
        Material::factory()->state(['workspace_id' => $this->workspaces[2]->id])->count(1)->create();


        $response = $this->get("api/v1/workspace/list?" . Arr::query(['orderBy' => 'materials_count', 'order' => -1]))->assertStatus(200)->json();

        $this->assertEquals($response['total'], 4);

        $attributeList = array_map(function ($data) {
            return $data['materials_count'];
        }, $response['results']);


        $sorted = $attributeList;
        rsort($sorted);

        $this->assertEquals($attributeList, $sorted);

        $this->assertEquals($response['results'][0]['id'], $this->workspaces[0]->id);
        $this->assertEquals($response['results'][1]['id'], $this->workspaces[1]->id);
        $this->assertEquals($response['results'][2]['id'], $this->workspaces[2]->id);
    }
}