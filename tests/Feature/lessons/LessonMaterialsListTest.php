<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;


class LessonMaterialsListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->lesson = Lesson::factory()->active()->create();

        // 2 Single materials with no group
        $this->lesson->materials()->attach(Material::factory()->sequence(
            ['tags' => 'fire'],
            ['tags' => 'smoke'],
            ['tags' => 'smoke;forest'],
            ['tags' => 'water']
        )->sequence(
                ['type' => 'material'],
                ['type' => 'recording'],
            )->count(4)->create());
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson/{$this->lesson->id}/materials")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/lesson/{$this->lesson->id}/materials")->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->get("api/v1/lesson/99/materials")->assertStatus(404);
    }

    /** @test */
    public function wrong_parameters_422(): void
    {
        array_map(function ($input) {
            $this->get("api/v1/material/list?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);

        $this->get("api/v1/material/list?" . Arr::query(['type' => "not_valid"]))->assertStatus(422); // Wrong type
        $this->get("api/v1/material/list?" . Arr::query(['tags' => "no_array"]))->assertStatus(422); // Not an array
    }



    /** @test */
    public function get_all_lesson_materials_200(): void
    {
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query([]))->assertStatus(200);

        $this->assertEquals($data['total'], 4);
        $this->assertEquals(count($data['results']), 4);
    }

    /** @test */
    public function get_all_materials_detail_200(): void
    {
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['limit' => 1]))->assertStatus(200);

        $this->assertEquals(count($data['results']), 1);
        $material = $this->lesson->materials()->where('material_id', $data['results'][0]['material_id'])->first();

        $this->assertNotNull($material);
        $this->assertEquals($data['results'][0]['material_id'], $material->id);
        $this->assertEquals($data['results'][0]['name'], $material->name);
        $this->assertEquals($data['results'][0]['type'], $material->type);
        $this->assertEquals($data['results'][0]['tags'], $material->tags);
        $this->assertFalse(isset($data['results'][0]['url'])); // We don't expose the URL in this end point
    }


    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->json();
        $data1 = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->json();
        $data2 = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->json();
        $data3 = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->json();

        // Verify that each page we return a different object
        $ids = [$data1['results'][0]['material_id'], $data2['results'][0]['material_id'], $data3['results'][0]['material_id'], $data['results'][0]['material_id']];

        $this->assertEquals(count(array_unique($ids)), 4);
        $this->assertEquals(count($data['results']), 1);
        $this->assertEquals($data['total'], 4);
        $this->assertEquals($data1['total'], 4);
    }



    /** @test */

    public function default_order_200(): void
    {
        $dataResponse = $this->get("api/v1/lesson/{$this->lesson->id}/materials?")->assertStatus(200)->json();
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

            $dataResponse = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['orderBy' => $orderBy, 'order' => -1]))->assertStatus(200)->json();

            $attributeList = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributeList;
            rsort($sorted);

            $this->assertEquals($attributeList, $sorted);
        }, ['name', 'created_at', 'updated_at']);
    }

    /** @test */
    public function order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['order' => 1, 'orderBy' => 'name']))->assertStatus(200)->json();
        $updatedAt = array_map(function ($data) {
            return $data['name'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }

    /** @test */
    public function search_by_content_material_name_200(): void
    {
        $material = $this->lesson->materials()->first();

        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['content' => substr($material->name, 0, 3)]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['material_id'], $material->id);
    }

    /** @test */
    public function search_by_tags_200(): void
    {
        $material = $this->lesson->materials()->first();

        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['tags' => explode(',', $material->tags)]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['material_id'], $material->id);

        // Smoke is in 2 tags
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['tags' => ['smoke']]))->assertStatus(200);
        $this->assertEquals($data['total'], 2);
    }

    /** @test */
    public function filter_by_type_200(): void
    {

        // Material
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['type' => 'recording']))->assertStatus(200);

        $this->assertEquals($data['total'], 2);
        $this->assertEquals($data['results'][0]['type'], 'recording');
        $this->assertEquals($data['results'][1]['type'], 'recording');

        // Material
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?" . Arr::query(['type' => 'material']))->assertStatus(200);

        $this->assertEquals($data['total'], 2);
        $this->assertEquals($data['results'][0]['type'], 'material');
        $this->assertEquals($data['results'][1]['type'], 'material');

    }


    /** @test */
    public function only_my_lesson_materials_200(): void
    {
        $lesson2 = Lesson::factory()->active()->create();
        $lesson2->materials()->attach(Material::factory()->count(3)->create());


        $data = $this->get("api/v1/lesson/{$this->lesson->id}/materials?")->assertStatus(200);

        $this->assertEquals($data['total'], 4);

        $data = $this->get("api/v1/lesson/{$lesson2->id}/materials?")->assertStatus(200);

        $this->assertEquals($data['total'], 3);
    }
}