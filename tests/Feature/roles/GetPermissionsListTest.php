<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class GetPermissionsListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $permissions;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        // Are generated by the Seeder
        $this->permissions = Permission::query()->get();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/role/permissions")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/role/permissions")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        array_map(function ($input) {
            $data = $this->get("api/v1/role/permissions?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);
    }


    /** @test */
    public function get_permissions_200(): void
    {
        $response = $this->get("api/v1/role/permissions", )->assertStatus(200);

        // The permission need to much the seeder
        $this->assertEquals(count($response['results']), count(PermissionSeeder::$permissions));
        $this->assertEquals($response['total'], count(PermissionSeeder::$permissions));
    }

    /** @test */
    public function get_permission_info_200(): void
    {
        $response = $this->get("api/v1/role/permissions?" . Arr::query(['content' => $this->permissions[0]->name]), );

        $this->assertEquals($response['total'], 1);
        $this->assertEquals($response['results'][0]['id'], $this->permissions[0]->id);
        $this->assertEquals($response['results'][0]['name'], $this->permissions[0]->name);
        $this->assertEquals($response['results'][0]['alias_name'], $this->permissions[0]->alias_name);
        $this->assertEquals($response['results'][0]['guard_name'], $this->permissions[0]->guard_name);
        $this->assertNotNull($response['results'][0]['created_at']);
        $this->assertNotNull($response['results'][0]['updated_at']);
        $this->assertEquals($response['results'][0]['category'], $this->permissions[0]->category);
    }

    /** @test */
    public function get_permission_by_content_200(): void
    {
        $list = $this->permissions->toArray();
        shuffle($list);
        $permission = $list[0];

        // By Name
        $response = $this->get("api/v1/role/permissions?" . Arr::query(['content' => substr($permission['name'], 0, 3)]), );
        $this->assertEquals($response['results'][0]['id'], $permission['id']);

        // By Alias Name
        $response = $this->get("api/v1/role/permissions?" . Arr::query(['content' => substr($permission['alias_name'], 0, 3)]), );
        $this->assertEquals($response['results'][0]['id'], $permission['id']);

        // By Category
        $response = $this->get("api/v1/role/permissions?" . Arr::query(['content' => substr($permission['category'], 0, 3)]), );
        $this->assertEquals($response['results'][0]['category'], $permission['category']);
    }


    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/role/permissions?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200);
        $data1 = $this->get("api/v1/role/permissions?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200);
        $data2 = $this->get("api/v1/role/permissions?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200);
        $data3 = $this->get("api/v1/role/permissions?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200);

        // Verify that each page we return a different object
        $ids = [$data1['results'][0]['id'], $data2['results'][0]['id'], $data3['results'][0]['id'], $data['results'][0]['id']];

        $this->assertEquals(count(array_unique($ids)), 4);
        $this->assertEquals(count($data['results']), 1);

        $this->assertEquals($data['total'], count(PermissionSeeder::$permissions));
        $this->assertEquals($data1['total'], count(PermissionSeeder::$permissions));
    }



    /** @test */

    public function default_order_200(): void
    {
        $dataResponse = $this->get("api/v1/role/permissions?")->assertStatus(200);
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
            $dataResponse = $this->get("api/v1/role/permissions?" . Arr::query(['orderBy' => $orderBy, 'order' => -1]))->assertStatus(200);

            $attributePermissions = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributePermissions;
            rsort($sorted);

            $this->assertEquals($attributePermissions, $sorted, "Order by failed for {$orderBy}");
        }, ['name', 'alias_name', 'category', 'created_at', 'updated_at']);
    }

    /** @test */
    public function default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/role/permissions?" . Arr::query(['order' => 1]))->assertStatus(200);
        $updatedAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }
}