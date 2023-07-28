<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GroupListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $groups;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->groups = Group::factory()->count(4)->sequence(
            ['created_at' => now()->addSeconds(2)],
            ['created_at' => now()->addSeconds(5)],
            ['created_at' => now()->addSeconds(8)],
            ['created_at' => now()->addSeconds(10)],
        )->create();
    }



    /** @test */
    public function wrong_params_422(): void
    {
        $this->get("api/v1/group/list?" . Arr::query(['names' => 123]))->assertStatus(422);
        $this->get("api/v1/group/list?" . Arr::query(['colors' => 123]))->assertStatus(422);
        $this->get("api/v1/group/list?" . Arr::query(['codes' => 123]))->assertStatus(422);
        $this->get("api/v1/group/list?" . Arr::query(['colors' => ['9']]))->assertStatus(422);
        $this->get("api/v1/group/list?" . Arr::query(['codes' => ['???']]))->assertStatus(422);
        $this->get("api/v1/group/list?" . Arr::query(['names' => ['???']]))->assertStatus(422);

        array_map(function ($input) {
            $this->get("api/v1/workspace/list?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);
    }
    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/group/list")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/group/list")->assertStatus(403);
    }


    /** @test */
    public function find_all_items_200(): void
    {
        $data = $this->get("api/v1/group/list", [])->assertStatus(200)->decodeResponseJson();
        $this->assertEquals(count($data['results']), 4);
        $this->assertEquals($data['total'], 4);
    }

    /** @test */
    public function find_by_name_200(): void
    {
        $data = $this->get("api/v1/group/list?" . Arr::query(['names' => [$this->groups[0]->name]]))->assertStatus(200)->decodeResponseJson();
        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['id'], $this->groups[0]->id);
    }

    /** @test */
    public function find_by_code_200(): void
    {
        $data = $this->get("api/v1/group/list?" . Arr::query(['codes' => [$this->groups[0]->code]]))->assertStatus(200)->decodeResponseJson();
        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['id'], $this->groups[0]->id);
    }

    /** @test */
    public function find_by_color_200(): void
    {
        $data = $this->get("api/v1/group/list?" . Arr::query(['colors' => [$this->groups[0]->color]]))->assertStatus(200)->decodeResponseJson();
        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['id'], $this->groups[0]->id);
    }

    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/group/list?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->decodeResponseJson();

        $data1 = $this->get("api/v1/group/list?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->decodeResponseJson();
        $data2 = $this->get("api/v1/group/list?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->decodeResponseJson();
        $data3 = $this->get("api/v1/group/list?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->decodeResponseJson();

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
        $dataResponse = $this->get("api/v1/group/list?")->assertStatus(200)->decodeResponseJson();
        $createdAt = array_map(function ($data) {
            return $data['created_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        rsort($sorted);

        $this->assertEquals($createdAt, $sorted);
    }

    /** @test */
    public function default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/group/list?" . Arr::query(['order' => 1]))->assertStatus(200)->decodeResponseJson();
        $createdAt = array_map(function ($data) {
            return $data['created_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        sort($sorted);

        $this->assertEquals($createdAt, $sorted);
    }


    /** @test */

    public function order_by_desc_200(): void
    {
        $nameOrderResponse = $this->get("api/v1/group/list?" . Arr::query(['orderBy' => 'name', 'order' => 1]))->assertStatus(200)->decodeResponseJson();

        $name = array_map(function ($data) {
            return $data['name'];
        }, $nameOrderResponse['results'], );

        $nameSorted = $name;
        sort($nameSorted);

        $this->assertEquals($name, $nameSorted);
    }

    /** @test */

    public function order_by_asc_200(): void
    {
        $nameOrderResponse = $this->get("api/v1/group/list?" . Arr::query(['orderBy' => 'name', 'order' => -1]))->assertStatus(200)->decodeResponseJson();

        $name = array_map(function ($data) {
            return $data['name'];
        }, $nameOrderResponse['results'], );

        $nameSorted = $name;
        rsort($nameSorted);

        $this->assertEquals($name, $nameSorted);
    }

    /** @test */
    public function search_by_content_200(): void
    {
        $data1 = $this->get("api/v1/group/list?" . Arr::query(['content' => substr($this->groups[0]->name, 0, 3)]))->assertStatus(200)->decodeResponseJson();

        $this->assertEquals($data1['results'][0]['id'], $this->groups[0]->id);

        $data2 = $this->get("api/v1/group/list?" . Arr::query(['content' => substr($this->groups[2]->code, 0, 3)]))->assertStatus(200)->decodeResponseJson();


        $this->assertEquals($data2['results'][0]['id'], $this->groups[2]->id);
    }


    /** @test */
    public function return_number_active_students_200(): void
    {
        GroupUsers::factory()->group($this->groups[0])->count(4)->create();
        GroupUsers::factory()->group($this->groups[0])->discharged()->count(2)->create();
        $data = $this->get("api/v1/group/list?" . Arr::query(['content' => substr($this->groups[0]->name, 0, 3)]))->assertStatus(200)->decodeResponseJson();
        $this->assertEquals(GroupUsers::query()->where('group_id', $this->groups[0]->id)->count(), 6);
        $this->assertEquals($data['results'][0]['active_users'], 4);
    }


}