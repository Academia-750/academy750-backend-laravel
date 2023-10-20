<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class StudentLessonInfoTest extends TestCase
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

        $this->user = User::factory()->student()->allowedTo(Permission::SEE_LESSONS)->create();



        $this->lesson = Lesson::factory()
            ->withStudents($this->user)
            ->create(['url' => $this->faker()->url()]);


        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(403);
    }



    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->get("api/v1/student-lessons/99/info")->assertStatus(404); //  Content not an string
    }

    /** @test */
    public function student_not_assigned_403(): void
    {

        $lesson = Lesson::factory()
            ->create(['url' => $this->faker()->url()]);

        $this->get("api/v1/student-lessons/{$lesson->id}/info")->assertStatus(403);

    }




    /** @test */
    public function student_lesson_content_200(): void
    {

        $data = $this->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(200);
        $lesson = $data['result'];

        $this->assertEquals($lesson['id'], $this->lesson->id);
        $this->assertEquals($lesson['name'], $this->lesson->name);
        $this->assertEquals($lesson['description'], $this->lesson->description);
        $this->assertEquals($lesson['is_online'], $this->lesson->is_online);
        $this->assertEquals($lesson['is_active'], $this->lesson->is_active);
        $this->assertEquals($lesson['date'], $this->lesson->date);
        $this->assertEquals($lesson['start_time'], $this->lesson->start_time);
        $this->assertEquals($lesson['end_time'], $this->lesson->end_time);
        $this->assertEquals($lesson['will_join'], false);
        $this->assertEquals($lesson['color'], null);
        $this->assertEquals($lesson['user_id'], $this->user->id);
        // URL is hidden, there is specific API for users with permissions for it
        $this->assertFalse(isset($lesson['url']));
    }

    /** @test */
    public function student_lesson_active_200(): void
    {

        $this->lesson->update(['is_active' => true]);

        $data = $this->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(200);
        $lesson = $data['result'];

        $this->assertEquals($lesson['is_active'], true);
    }

    /** @test */
    public function student_lesson_online_200(): void
    {

        $this->lesson->update(['is_online' => true]);

        $data = $this->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(200);
        $lesson = $data['result'];

        $this->assertEquals($lesson['is_online'], true);
    }

    /** @test */
    public function has_joined_lesson_200(): void
    {

        $this->lesson->students()->updateExistingPivot($this->user->id, ['will_join' => true]);

        $data = $this->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(200);
        $lesson = $data['result'];

        $this->assertEquals($lesson['id'], $this->lesson->id);
        $this->assertEquals($lesson['will_join'], true);

    }


    /** @test */
    public function group_color_200(): void
    {
        $group = Group::factory()->create();
        GroupUsers::factory()->group($group)->count(3)->create();


        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group->id])->assertStatus(200);


        $data = $this->actingAs($this->user)->get("api/v1/student-lessons/{$this->lesson->id}/info")->assertStatus(200);

        $this->assertEquals($data['result']['color'], $group->color); // People from group 1 are more number
    }

}