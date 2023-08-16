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


class DeleteGroupFromLessonTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;
    private $group;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->lesson = Lesson::factory()->active()->create();

        $this->group = Group::factory()->create();

        $students = GroupUsers::factory()->group($this->group)->count(2)->create();
        GroupUsers::factory()->group($this->group)->discharged()->count(1)->create();

        $this->lesson->students()->attach($students, ['group_id' => $this->group->id, 'group_name' => $this->group->name]);


    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/lesson/{$this->lesson->id}/group")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/lesson/{$this->lesson->id}/group")->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->delete("api/v1/lesson/99/group", ['group_id' => $this->group->id])->assertStatus(404);
    }

    /** @test */
    public function group_not_found_404(): void
    {
        $this->delete("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => '9999'])->assertStatus(404);
    }

    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->delete("api/v1/lesson/{$this->lesson->id}/group", [])->assertStatus(422); // Missing group_id
        $this->delete("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => []])->assertStatus(422); // Wrong type
    }



    /** @test */
    public function delete_group_from_lesson_200(): void
    {
        $data = $this->delete("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200)->json();

        $this->assertEquals($data['count'], 2);

        $this->assertEquals($this->lesson->students()->count(), 0);
    }

    /** @test */
    public function delete_group_not_affect_single_student_200(): void
    {

        // Individual Student (no group)
        $this->lesson->students()->attach(User::factory()->student()->create());

        $data = $this->delete("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200)->json();

        $this->assertEquals($data['count'], 2);
        $this->assertEquals($this->lesson->students()->count(), 1);
    }

    /** @test */
    public function delete_group_not_affect_other_groups_200(): void
    {

        $group = Group::factory()->create();
        $students = GroupUsers::factory()->group($group)->count(3)->create();
        $this->lesson->students()->attach($students, ['group_id' => $group->id, 'group_name' => $group->name]);


        $data = $this->delete("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200)->json();

        $this->assertEquals($data['count'], 2);
        $this->assertEquals($this->lesson->students()->count(), 3);
        // Remaining 3 students are all from the group which was not deleted
        $this->assertEquals($this->lesson->students()->pluck('group_id')->toArray(), [$group->id, $group->id, $group->id]);
    }
}