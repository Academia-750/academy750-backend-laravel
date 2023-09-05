<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class LessonInfoTest extends TestCase
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

    }


    /** @test */
    public function not_route_405(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(401);
    }

    /** @test */
    public function students_can_access_200(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200);
    }


    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->get("api/v1/lesson/99")->assertStatus(404);
    }

    /** @test */
    public function get_lesson_200(): void
    {

        $data = $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200)->json();

        $this->assertEquals($data['result']['id'], $this->lesson->id);
        $this->assertEquals($data['result']['name'], $this->lesson->name);
        $this->assertEquals($data['result']['description'], $this->lesson->description);
        $this->assertEquals($data['result']['is_online'], $this->lesson->is_online);
        $this->assertEquals($data['result']['is_active'], $this->lesson->is_active);
        $this->assertEquals($data['result']['date'], $this->lesson->date);
        $this->assertEquals($data['result']['start_time'], $this->lesson->start_time);
        $this->assertEquals($data['result']['end_time'], $this->lesson->end_time);
        $this->assertEquals($data['result']['color'], null);

    }

    /** @test */
    public function get_lesson_color_200(): void
    {

        $group = Group::factory()->create();
        GroupUsers::factory()->group($group)->count(2)->create();
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group->id])->assertStatus(200);

        $data = $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200);

        $this->assertEquals($data['result']['color'], $group->color);
    }

    /** @test */
    public function get_lesson_groups_200(): void
    {

        $group = Group::factory()->create();
        GroupUsers::factory()->group($group)->count(1)->create();
        $group2 = Group::factory()->create();
        GroupUsers::factory()->group($group2)->count(1)->create();

        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group->id])->assertStatus(200);
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group2->id])->assertStatus(200);


        $data = $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200);

        $this->assertEquals($data['result']['groups'][0]['group_id'], $group->id);
        $this->assertEquals($data['result']['groups'][0]['group_name'], $group->name);

        $this->assertEquals($data['result']['groups'][1]['group_id'], $group2->id);
        $this->assertEquals($data['result']['groups'][1]['group_name'], $group2->name);
    }


    /** @test */
    public function get_lesson_several_groups_color_200(): void
    {

        $group = Group::factory()->create();
        GroupUsers::factory()->group($group)->count(3)->create();
        $group2 = Group::factory()->create();
        GroupUsers::factory()->group($group2)->count(2)->create();
        $group3 = Group::factory()->create();
        GroupUsers::factory()->group($group3)->count(1)->create();

        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group->id])->assertStatus(200);
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group2->id])->assertStatus(200);
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group3->id])->assertStatus(200);


        $data = $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200);

        $this->assertEquals($data['result']['color'], $group->color);
    }


    /** @test */
    public function get_student_count_200(): void
    {

        $group = Group::factory()->create();
        GroupUsers::factory()->group($group)->count(3)->create();


        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group->id])->assertStatus(200);

        $data = $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200);

        $this->assertEquals($data['result']['student_count'], 3);
    }

    /** @test */
    public function get_will_join_count_200(): void
    {
        $this->lesson->students()->attach(User::factory()->student()->count(2)->create(), ['will_join' => true]);

        $data = $this->get("api/v1/lesson/{$this->lesson->id}"); // ->assertStatus(200);

        $this->assertEquals($data['result']['will_join_count'], 2);
    }

}