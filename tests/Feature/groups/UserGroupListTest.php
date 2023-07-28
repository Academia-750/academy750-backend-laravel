<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserGroupListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $group;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->group = Group::factory()->create();

        // To Query
        GroupUsers::factory()
            ->count(4)
            ->group($this->group)
            ->state(new Sequence(function ($sequence) {
                return ['user_id' => User::factory()->create()->id, 'created_at' => now()->addSeconds($sequence->index + 1)];
            }))
            ->create();

        GroupUsers::factory()
            ->group($this->group)
            ->discharged()
            ->state(new Sequence(function ($sequence) {
                return ['user_id' => User::factory()->create()->id, 'created_at' => now()->addSeconds($sequence->index + 1)];
            }))
            ->count(3)
            ->create();

        // To Make Noise
        GroupUsers::factory()
            ->count(2)
            ->group(Group::factory()->create())
            ->state(new Sequence(function () {
                return ['user_id' => User::factory()->create()->id];
            }))
            ->create();

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/group/{$this->group->id}/list", [])->assertStatus(401);
    }


    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/group/{$this->group->id}/list", [])->assertStatus(403);
    }


    /** @test */
    public function wrong_params_422(): void
    {
        $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['orderBy' => 'random']))->assertStatus(422);
        $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['order' => 0]))->assertStatus(422);
        $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['order' => 2]))->assertStatus(422);
        $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['offset' => -10]))->assertStatus(422);
        $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['limit' => -10]))->assertStatus(422);
    }


    /** @test */
    public function group_not_found_200(): void
    {
        /** Wrong group id won't fire any error just return empty data */
        $response = $this->get("api/v1/group/99/list", [])->assertStatus(200);
        $this->assertEquals($response['total'], 0);
        $this->assertEmpty($response['results']);
    }


    /** @test */
    public function group_users_200(): void
    {

        $response = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query([]))->assertStatus(200);
        $this->assertEquals($response['total'], 4);

        // All are in the same group
        array_map(function ($item) {
            $this->assertEquals($item['group_id'], $this->group->id);
            $this->assertEquals($item['name'], $this->group->name);
            $this->assertEmpty($item['discharged_at']);

        }, $response['results']);

        // Check that the join works
        $user1 = User::find($response['results'][0]['user_id']);
        $this->assertNotEmpty($user1);
        $this->assertEquals($user1->full_name, $response['results'][0]['full_name']);
        $this->assertEquals($user1->dni, $response['results'][0]['dni']);
        $this->assertEquals($user1->phone, $response['results'][0]['phone']);
        $this->assertEquals($user1->email, $response['results'][0]['email']);
        $this->assertEquals($user1->uuid, $response['results'][0]['uuid']);


    }

    /** @test */
    public function group_discharged_users_200(): void
    {

        $response = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['discharged' => true]))->assertStatus(200);
        $this->assertEquals($response['total'], 3);

        array_map(function ($item) {
            $this->assertEquals($item['group_id'], $this->group->id);
            $this->assertNotEmpty($item['discharged_at']);
        }, $response['results']);
    }



    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->decodeResponseJson();


        $data1 = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->decodeResponseJson();
        $data2 = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->decodeResponseJson();
        $data3 = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->decodeResponseJson();

        // Verify that each page we return a different object
        $ids = [$data1['results'][0]['id'], $data2['results'][0]['id'], $data3['results'][0]['id'], $data['results'][0]['id']];

        $this->assertEquals(count(array_unique($ids)), 4);
        $this->assertEquals(count($data['results']), 1);
        // Check that despite we return 1 item the total is correct
        $this->assertEquals($data['total'], 4);
        $this->assertEquals($data1['total'], 4);


    }


    /** @test */
    public function default_order_200(): void
    {
        $dataResponse = $this->get("api/v1/group/{$this->group->id}/list?")->assertStatus(200)->decodeResponseJson();
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
        $dataResponse = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['order' => 1]))->assertStatus(200)->decodeResponseJson();
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
        $nameOrderResponse = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['orderBy' => 'dni', 'order' => 1]))->assertStatus(200)->decodeResponseJson();

        $name = array_map(function ($data) {
            return $data['dni'];
        }, $nameOrderResponse['results'], );

        $nameSorted = $name;
        sort($nameSorted);

        $this->assertEquals($name, $nameSorted);
    }

    /** @test */

    public function order_by_asc_200(): void
    {
        $nameOrderResponse = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['orderBy' => 'full_name', 'order' => -1]))->assertStatus(200)->decodeResponseJson();

        $name = array_map(function ($data) {
            return $data['full_name'];
        }, $nameOrderResponse['results'], );

        $nameSorted = $name;
        rsort($nameSorted);

        $this->assertEquals($name, $nameSorted);
    }

    /** @test */
    public function search_by_content_200(): void
    {
        $item = GroupUsers::query()->where('group_id', $this->group->id)->whereNull('discharged_at')->inRandomOrder()->first();

        // Search by DNI
        $data = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['content' => substr($item->user->dni, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($data['results'][0]['id'], $item->id);

        // Search by user first_name
        $data = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['content' => substr($item->user->full_name, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($data['results'][0]['id'], $item->id);

        // Search by user email
        $data = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['content' => substr($item->user->email, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($data['results'][0]['id'], $item->id);

        // Search by user phone
        $data = $this->get("api/v1/group/{$this->group->id}/list?" . Arr::query(['content' => substr($item->user->phone, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($data['results'][0]['id'], $item->id);

    }
}