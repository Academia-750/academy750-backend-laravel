<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;


class ListLessonStudentsTest extends TestCase
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

        // 2 Single students with no group
        $this->lesson->students()->attach(User::factory()->student()->count(4)->create());
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson/{$this->lesson->id}/students")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/lesson/{$this->lesson->id}/students")->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->get("api/v1/lesson/99/students")->assertStatus(404);
    }

    /** @test */
    public function wrong_parameters_422(): void
    {
        array_map(function ($input) {
            $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query($input))->assertStatus(422);
        }, $this->pagination_wrong_inputs);
    }



    /** @test */
    public function get_all_students_200(): void
    {
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query([]))->assertStatus(200);

        $this->assertEquals($data['total'], 4);
        $this->assertEquals(count($data['results']), 4);
    }

    /** @test */
    public function get_all_students_detail_200(): void
    {
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['limit' => 1]))->assertStatus(200);

        $this->assertEquals(count($data['results']), 1);
        $student = $this->lesson->students()->where('user_id', $data['results'][0]['user_id'])->first();

        $this->assertNotNull($student);
        $this->assertEquals($data['results'][0]['user_id'], $student->id);
        $this->assertEquals($data['results'][0]['uuid'], $student->uuid);
        $this->assertEquals($data['results'][0]['dni'], $student->dni);
        $this->assertEquals($data['results'][0]['full_name'], $student->full_name);
        $this->assertNull($data['results'][0]['group_id']);
        $this->assertNull($data['results'][0]['group_name']);
    }


    /** @test */
    public function pagination_200(): void
    {
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['limit' => 1, 'offset' => 0]))->assertStatus(200)->json();
        $data1 = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['limit' => 1, 'offset' => 1]))->assertStatus(200)->json();
        $data2 = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['limit' => 1, 'offset' => 2]))->assertStatus(200)->json();
        $data3 = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['limit' => 1, 'offset' => 3]))->assertStatus(200)->json();

        // Verify that each page we return a different object
        $ids = [$data1['results'][0]['user_id'], $data2['results'][0]['user_id'], $data3['results'][0]['user_id'], $data['results'][0]['user_id']];

        $this->assertEquals(count(array_unique($ids)), 4);
        $this->assertEquals(count($data['results']), 1);
        $this->assertEquals($data['total'], 4);
        $this->assertEquals($data1['total'], 4);
    }



    /** @test */

    public function default_order_200(): void
    {
        $dataResponse = $this->get("api/v1/lesson/{$this->lesson->id}/students?")->assertStatus(200)->json();
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

        $group = Group::factory()->create();
        $this->lesson->students()->attach(GroupUsers::factory()->group($group)->count(2)->create(), ['group_id' => $group->id, 'group_name' => $group->name]);

        $group2 = Group::factory()->create();
        $this->lesson->students()->attach(GroupUsers::factory()->group($group2)->count(2)->create(), ['group_id' => $group2->id, 'group_name' => $group2->name]);

        array_map(function ($orderBy) {

            $dataResponse = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['orderBy' => $orderBy, 'order' => -1]))->assertStatus(200)->json();

            $attributeList = array_map(function ($data) use ($orderBy) {
                return $data[$orderBy];
            }, $dataResponse['results']);

            $sorted = $attributeList;
            rsort($sorted);

            $this->assertEquals($attributeList, $sorted);
        }, ['dni', 'full_name', 'group_name', 'created_at', 'updated_at']);
    }

    /** @test */
    public function default_order_asc_200(): void
    {
        $dataResponse = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['order' => 1]))->assertStatus(200)->json();
        $updatedAt = array_map(function ($data) {
            return $data['updated_at'];
        }, $dataResponse['results'], );

        $sorted = $updatedAt;
        sort($sorted);

        $this->assertEquals($updatedAt, $sorted);
    }

    /** @test */
    public function search_by_content_user_200(): void
    {
        $student = $this->lesson->students()->first();

        // By DNI
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['content' => substr($student->dni, 0, 3)]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['user_id'], $student->id);

        // By Full Name
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['content' => substr($student->full_name, 0, 4)]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['user_id'], $student->id);


    }

    /** @test */
    public function search_by_content_group_200(): void
    {
        $group = Group::factory()->create();
        $this->lesson->students()->attach(GroupUsers::factory()->group($group)->count(2)->create(), ['group_id' => $group->id, 'group_name' => $group->name]);

        $group2 = Group::factory()->create();
        $this->lesson->students()->attach(GroupUsers::factory()->group($group2)->count(1)->create(), ['group_id' => $group2->id, 'group_name' => $group2->name]);


        // By DNI
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['content' => substr($group->name, 0, 3)]))->assertStatus(200);

        $this->assertEquals($data['total'], 2);
        $this->assertEquals($data['results'][0]['group_name'], $group->name);
        $this->assertEquals($data['results'][1]['group_name'], $group->name);

        // By Full Name
        $data = $this->get("api/v1/lesson/{$this->lesson->id}/students?" . Arr::query(['content' => substr($group2->name, 0, 3)]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['group_name'], $group2->name);
    }
}