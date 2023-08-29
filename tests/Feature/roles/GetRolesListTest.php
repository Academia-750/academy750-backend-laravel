<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class GetRoleListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $roles;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->roles = Role::factory()
            ->count(4)
            ->sequence(fn($sequence) => ['updated_at' => now()->add($sequence->index * 10, 'seconds')])
            ->sequence(
                ['name' => 'Teacher', 'alias_name' => 'teacher'],
                ['name' => 'Professional Training', 'alias_name' => 'professional_training'],
                ['name' => 'Assistant', 'alias_name' => 'assitant'],
                ['name' => 'Only Tests', 'alias_name' => 'only_tests'],
            )
            ->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/role/list")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/role/list")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        array_map(function ($input) {
            $this->get("api/v1/role/list?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);
    }


    /** @test */
    public function get_role_list_200(): void
    {
        $response = $this->get("api/v1/role/list", )->assertStatus(200);

        // The seeder always start with 2 basic roles: admin and student
        $this->assertEquals(count($response['results']), 4 + 2);
        $this->assertEquals($response['total'], 4 + 2);
    }

    /** @test */
    public function get_role_info_200(): void
    {
        $response = $this->get("api/v1/role/list?" . Arr::query(['content' => $this->roles[0]->name]), );

        $this->assertEquals($response['total'], 1);
        $this->assertEquals($response['results'][0]['id'], $this->roles[0]->id);
        $this->assertEquals($response['results'][0]['name'], $this->roles[0]->name);
        $this->assertEquals($response['results'][0]['alias_name'], $this->roles[0]->alias_name);
        $this->assertEquals($response['results'][0]['guard_name'], $this->roles[0]->guard_name);
        $this->assertNotNull($response['results'][0]['created_at']);
        $this->assertNotNull($response['results'][0]['updated_at']);
        $this->assertEquals($response['results'][0]['users_count'], 0);
        $this->assertEquals($response['results'][0]['protected'], 0);
        $this->assertEquals($response['results'][0]['default_role'], 0);
    }

    /** @test */
    public function get_roles_by_content_200(): void
    {

        $role = $this->roles[0];
        $response = $this->get("api/v1/role/list?" . Arr::query(['content' => substr($role->name, 0, 3)]), );
        $this->assertEquals($response['results'][0]['id'], $role->id);

        $role = $this->roles[1];
        $response = $this->get("api/v1/role/list?" . Arr::query(['content' => substr($role->name, 0, 3)]), );
        $this->assertEquals($response['results'][0]['id'], $role->id);
    }



    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/role/list?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200);
        $data1 = $this->get("api/v1/role/list?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200);
        $data2 = $this->get("api/v1/role/list?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200);
        $data3 = $this->get("api/v1/role/list?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200);

        // Verify that each page we return a different object
        $ids = [$data1['results'][0]['id'], $data2['results'][0]['id'], $data3['results'][0]['id'], $data['results'][0]['id']];

        $this->assertEquals(count(array_unique($ids)), 4);
        $this->assertEquals(count($data['results']), 1);

        // Seeder inits with 2 basic roles
        $this->assertEquals($data['total'], 4 + 2);
        $this->assertEquals($data1['total'], 4 + 2);
    }



    /** @test */

    public function default_order_200(): void
    {
        $dataResponse = $this->get("api/v1/role/list?")->assertStatus(200);
        $createdAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $createdAt;
        rsort($sorted);

        $this->assertEquals($createdAt, $sorted);
    }


    /** @test */
    public function roles_with_users_200(): void
    {

        // Default admin role has 1 user (the one used on the test)
        // Here we create more than 1 to make it work

        User::factory()->withRole($this->roles[0]->name)->count(3)->create();
        User::factory()->withRole($this->roles[1]->name)->count(2)->create();


        $dataResponse = $this->get("api/v1/role/list?" . Arr::query(['orderBy' => 'users_count', 'order' => -1]))->assertStatus(200);

        $this->assertEquals($dataResponse['results'][0]['id'], $this->roles[0]->id);
        $this->assertEquals($dataResponse['results'][0]['name'], $this->roles[0]->name);
        $this->assertEquals($dataResponse['results'][0]['users_count'], 3);

        $this->assertEquals($dataResponse['results'][1]['id'], $this->roles[1]->id);
        $this->assertEquals($dataResponse['results'][1]['name'], $this->roles[1]->name);
        $this->assertEquals($dataResponse['results'][1]['users_count'], 2);
    }

    /** @test */
    public function order_by_200(): void
    {
        array_map(function ($orderBy) {
            $dataResponse = $this->get("api/v1/role/list?" . Arr::query(['orderBy' => $orderBy, 'order' => -1]))->assertStatus(200);

            $attributeList = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributeList;
            rsort($sorted);

            $this->assertEquals($attributeList, $sorted);
        }, ['name', 'users_count', 'created_at', 'updated_at']);
    }

    /** @test */
    public function default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/role/list?" . Arr::query(['order' => 1]))->assertStatus(200);
        $updatedAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }


    /** @test */
    public function seeder_default_role(): void
    {
        $dataResponse = $this->get("api/v1/role/list?" . Arr::query(['content' => 'student']))->assertStatus(200);

        $this->assertEquals($dataResponse['results'][0]['name'], 'student');
        $this->assertEquals($dataResponse['results'][0]['default_role'], 1);
    }


    /** @test */
    public function seeder_default_admin_role(): void
    {
        $dataResponse = $this->get("api/v1/role/list?" . Arr::query(['content' => 'admin']))->assertStatus(200);

        $this->assertEquals($dataResponse['results'][0]['name'], 'admin');
        $this->assertEquals($dataResponse['results'][0]['protected'], 1);
    }
}