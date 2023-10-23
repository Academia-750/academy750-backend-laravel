<?php

namespace Tests\Feature;


use App\Models\Lesson;
use App\Models\Material;
use App\Models\Permission;
use App\Models\User;
use App\Models\Workspace;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class StudentLessonMaterialsListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lessons;

    private $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()
            ->student()
            ->allowedTo(
                [
                    Permission::SEE_LESSONS,
                    Permission::SEE_LESSON_MATERIALS,
                    Permission::SEE_LESSON_RECORDINGS
                ]
            )->create();


        // 2 Lessons, 2 materials 1 recording
        $this->lessons = Lesson::factory()
            ->withStudents($this->user)
            ->count(2)
            ->create(['is_active' => true])
            ->each(function ($lesson) {
                $lesson->materials()->attach(
                    Material::factory()
                        ->withUrl()
                        ->count(2)
                        ->sequence(['tags' => 'fire'], ['tags' => 'water'])
                        ->create(['type' => 'material'])
                );
                $lesson->materials()->attach(
                    Material::factory()
                        ->withUrl()
                        ->count(1)
                        ->sequence(['tags' => 'smoke'])
                        ->create(['type' => 'recording'])
                );
            });


        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/materials?")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material']))->assertStatus(403);
    }


    /** @test */
    public function no_permissions_for_type_403(): void
    {
        $user = User::factory()->student()->allowedTo([Permission::SEE_LESSONS])->create();

        $this->actingAs($user)->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material']))->assertStatus(403);
        $this->actingAs($user)->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'recording']))->assertStatus(403);

    }




    /** @test */
    public function wrong_parameters_422(): void
    {

        $this->get("api/v1/student-lessons/materials?" . Arr::query([]))->assertStatus(422); // No type

        array_map(function ($input) {
            $this->get("api/v1/student-lessons/materials?" . Arr::query(["type" => "material", ...$input]))->assertStatus(422);
        }, $this->pagination_wrong_inputs);

        $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => "not_valid"]))->assertStatus(422); // Wrong type
        $this->get("api/v1/student-lessons/materials?" . Arr::query(["type" => "material", 'tags' => "no_array"]))->assertStatus(422); // Not an array

        $this->get("api/v1/student-lessons/materials?" . Arr::query(["type" => "material", 'lessons' => "no_array"]))->assertStatus(422); // Not an array
        $this->get("api/v1/student-lessons/materials?" . Arr::query(["type" => "material", 'lessons' => ["no-id"]]))->assertStatus(422); // Not id

        $this->get("api/v1/student-lessons/materials?" . Arr::query(["type" => "material", 'workspaces' => "no_id"]))->assertStatus(422); // Not id
        $this->get("api/v1/student-lessons/materials?" . Arr::query(["type" => "material", 'workspaces' => [-33]]))->assertStatus(422); // Negative Value

    }




    /** @test */
    public function get_all_type_materials_200(): void
    {

        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material']))->assertStatus(200);

        $this->assertEquals(count($data['results']), 4);
        $this->assertEquals($data['total'], 4);
    }

    /** @test */
    public function get_all_type_recording_200(): void
    {

        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'recording']))->assertStatus(200);
        $this->assertEquals(count($data['results']), 2);
        $this->assertEquals($data['total'], 2);
    }

    /** @test */
    public function student_material_content_200(): void
    {

        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'limit' => 1]))->assertStatus(200);
        $this->assertEquals(count($data['results']), 1);
        $material = Material::find($data['results'][0]['material_id']);

        $this->assertNotNull($material);
        $this->assertEquals($data['results'][0]['material_id'], $material->id);
        $this->assertEquals($data['results'][0]['name'], $material->name);
        $this->assertEquals($data['results'][0]['type'], $material->type);
        $this->assertEquals($data['results'][0]['tags'], $material->tags);
        $this->assertEquals($data['results'][0]['workspace'], $material->workspace->name);

        $this->assertNotNull($data['results'][0]['created_at']);
        $this->assertNotNull($data['results'][0]['updated_at']);
        $this->assertFalse(isset($data['results'][0]['url'])); // We don't expose the URL in this end point
        $this->assertEquals($data['results'][0]['has_url'], 1);

    }



    /** @test */
    public function filter_by_lesson_id_200(): void
    {
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$this->lessons[0]->id]]))->assertStatus(200);
        $this->assertEquals($response['total'], 2);
        $this->assertNotNull($this->lessons[0]->materials()->find($response['results'][0]['material_id']));
        $this->assertNotNull($this->lessons[0]->materials()->find($response['results'][1]['material_id']));


        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$this->lessons[1]->id]]))->assertStatus(200);

        $this->assertEquals($response['total'], 2);
        $this->assertNotNull($this->lessons[1]->materials()->find($response['results'][0]['material_id']));
        $this->assertNotNull($this->lessons[1]->materials()->find($response['results'][1]['material_id']));

        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$this->lessons[0]->id, $this->lessons[1]->id]]))->assertStatus(200);
        $this->assertEquals($response['total'], 4);

    }

    /** @test */
    public function filter_by_workspace_id_200(): void
    {
        $workspace = Workspace::factory()->create();
        // This lesson got already 2 materials. Now in total got 5, only 3 from the workspace
        $this->lessons[0]->materials()->attach(
            Material::factory()
                ->withUrl()
                ->count(3)
                ->create(['type' => 'material', 'workspace_id' => $workspace->id])
        );

        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'workspaces' => [$workspace->id]]))->assertStatus(200);

        $this->assertEquals($response['total'], 3);

        // All 3 results belong to the workspace that we searched
        $worksapces = $this->map($response['results'], 'workspace');
        $this->assertEquals($worksapces, [$workspace->name, $workspace->name, $workspace->name]);

        // Search with 2 worskaces, second has nothing
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'workspaces' => [$workspace->id, 99]]))->assertStatus(200);
        $this->assertEquals($response['total'], 3);

    }

    /** @test */
    public function filter_by_several_workspace_id_200(): void
    {
        $lesson1Workspaces = $this->map($this->lessons[0]->materials()->get()->toArray(), 'workspace_id');

        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'workspaces' => $lesson1Workspaces]))->assertStatus(200);

        $this->assertEquals($response['total'], 2);

        $names = Workspace::query()->whereIn('id', $lesson1Workspaces)->pluck('name');

        $this->assertContains($response['results'][0]['workspace'], $names);
        $this->assertContains($response['results'][1]['workspace'], $names);
    }


    /** @test */
    public function filter_by_lesson_complex_200(): void
    {
        // There was a bug cos the query was returning the users that didnt have this lesson.
        // Here we reproduce adding noise from other users
        User::factory()->count(3)->create();
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$this->lessons[0]->id]]))->assertStatus(200);

        $this->assertEquals($response['total'], 2);
    }

    /** @test */
    public function filter_by_lesson_403(): void
    {
        $lesson = Lesson::factory()->create();
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$lesson->id]]))->assertStatus(403);
        $this->assertStringContainsString($lesson->id, $response['error']);

        // Multiple Lessons
        $lesson2 = Lesson::factory()->create();
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$lesson->id, $lesson2->id]]))->assertStatus(403);
        $this->assertStringContainsString($lesson->id, $response['error']);
        $this->assertStringContainsString($lesson2->id, $response['error']);


        // Some lesson are ok others not
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$lesson->id, $this->lessons[0]->id]]))->assertStatus(403);
        $this->assertStringContainsString($lesson->id, $response['error']);
        $this->assertStringNotContainsString($this->lessons[0]->id, $response['error']);
    }


    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'limit' => 1, 'offset' => 0])); // ->assertStatus(200);

        $data1 = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'limit' => 1, 'offset' => 1]))->assertStatus(200);
        $data2 = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'limit' => 1, 'offset' => 2]))->assertStatus(200);
        $data3 = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'limit' => 1, 'offset' => 3]))->assertStatus(200);

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
        $dataResponse = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material']))->assertStatus(200);
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
            $dataResponse = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'orderBy' => $orderBy, 'order' => -1]))->assertStatus(200);

            $attributeList = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributeList;
            rsort($sorted);

            $this->assertEquals($attributeList, $sorted);
        }, ['name', 'workspace']);
    }

    /** @test */
    public function material_default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'order' => 1]))->assertStatus(200);
        $updatedAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }

    /** @test */
    public function content_200(): void
    {

        // Try Name lesson 0
        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query([
            'type' => 'material',
            'content' => substr($this->lessons[0]->materials()->first()->name, 0, 3)
        ]))->assertStatus(200);


        $this->assertNotNull($this->lessons[0]->materials()->find($data['results'][0]['material_id']));
        $this->assertEquals($data['total'], 1);

        // Try Name lesson 1
        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query([
            'type' => 'material',
            'content' => substr($this->lessons[1]->materials()->first()->name, 0, 3)
        ]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertNotNull($this->lessons[1]->materials()->find($data['results'][0]['material_id']));
    }

    /** @test */
    public function has_url_is_false_200(): void
    {
        $material = $this->lessons[0]->materials()->first();
        $material->url = '';
        $material->save();

        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query([
            'type' => 'material',
            'content' => $material->name
        ]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertFalse(isset($data['results'][0]['url'])); // We don't expose the URL in this end point
        $this->assertEquals($data['results'][0]['has_url'], 0);
    }

    /** @test */
    public function can_not_see_other_lesson_materials_200(): void
    {
        $lessons = Lesson::factory()
            ->count(2)
            ->create(['is_active' => true])
            ->each(function ($lesson) {
                $lesson->materials()->attach(Material::factory()->withUrl()->count(2)->create(['type' => 'material']));
                $lesson->materials()->attach(Material::factory()->withUrl()->count(1)->create(['type' => 'recording']));
            });


        $data = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material']))->assertStatus(200);
        $this->assertEquals($data['total'], 4);

        $materialIds = $this->map($data['results'], 'material_id');

        $count = DB::table('lesson_material')->whereIn('lesson_id', $lessons->pluck('id'))->whereIn('material_id', $materialIds)->count();

        $this->assertEquals($count, 0);


    }

    /** @test */
    public function filter_by_tags_200(): void
    {

        // Tags and material type
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'tags' => ['Fire']]), )->assertStatus(200);

        $this->assertEquals($response['total'], 2);
        $this->assertEquals($response['results'][0]['tags'], 'fire');
        $this->assertEquals($response['results'][1]['tags'], 'fire');


        // Tags not used by this type
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'tags' => ['smoke']]), )->assertStatus(200);

        $this->assertEquals($response['total'], 0);

        // Tags and recording type
        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'recording', 'tags' => ['smoke']]), )->assertStatus(200);

        $this->assertEquals($response['total'], 2);
        $this->assertEquals($response['results'][0]['tags'], 'smoke');
        $this->assertEquals($response['results'][1]['tags'], 'smoke');

    }


    /** @test */
    public function can_not_see_materials_from_non_active_lessons_200(): void
    {
        $noActiveLesson = Lesson::factory()
            ->withStudents($this->user)
            ->create(['is_active' => false]); // This is the matter!

        $noActiveLesson->materials()->attach(Material::factory()->withUrl()->count(2)->create(['type' => 'material']));

        $response = $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$noActiveLesson->id]]))->assertStatus(200);

        $this->assertEquals($response['total'], 0);

    }


    /** @test */
    public function filter_by_not_allowed_lessons_403(): void
    {
        $lesson = Lesson::factory()->create(['is_active' => true]);
        $this->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material', 'lessons' => [$lesson->id]]))->assertStatus(403);
    }


    /** @test */
    public function materials_dont_appear_duplicated_200(): void
    {
        // We use new user in this test to make it more simple
        $user = User::factory()
            ->student()
            ->allowedTo(
                [
                    Permission::SEE_LESSONS,
                    Permission::SEE_LESSON_MATERIALS,
                    Permission::SEE_LESSON_RECORDINGS
                ]
            )->create();

        // We create 2 materials
        $materials = Material::factory()
            ->withUrl()
            ->count(2)
            ->sequence(['tags' => 'fire'], ['tags' => 'water'])
            ->create(['type' => 'material']);

        // The 2 lesson has the exactly 2 materials
        $this->lessons = Lesson::factory()
            ->withStudents($user)
            ->count(2)
            ->create(['is_active' => true])
            ->each(function ($lesson) use ($materials) {
                $lesson->materials()->attach($materials);
            });

        $response = $this->actingAs($user)->get("api/v1/student-lessons/materials?" . Arr::query(['type' => 'material']))->assertStatus(200);

        // The user has 2 materials in 2 lessons, but is only 2 materials not 4 (as they are the same)
        $this->assertEquals($response['total'], 2);
        $this->assertEquals(count($response['results']), 2);

    }

}
