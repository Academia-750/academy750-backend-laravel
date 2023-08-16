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


class AddGroupToLessonTest extends TestCase
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

        GroupUsers::factory()->group($this->group)->count(4)->create();
        GroupUsers::factory()->group($this->group)->discharged()->count(2)->create();
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/lesson/{$this->lesson->id}/group")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/lesson/{$this->lesson->id}/group")->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->post("api/v1/lesson/99/group", ['group_id' => $this->group->id])->assertStatus(404);
    }

    /** @test */
    public function group_not_found_404(): void
    {
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => '9999'])->assertStatus(404);
    }

    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->post("api/v1/lesson/{$this->lesson->id}/group", [])->assertStatus(422); // Missing group_id
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => []])->assertStatus(422); // Wrong type
    }



    /** @test */
    public function group_join_lesson_200(): void
    {
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200)->json();

        $this->assertEquals($data['count'], 4); // Only Active users

        // Check are the right 4 users
        $activeUserIds = $this->group->users()->whereNull('discharged_at')->orderBy('user_id')->pluck('user_id');
        $lessonsUserIds = $this->lesson->students()->orderBy('user_id')->pluck('user_id');

        $this->assertEquals($activeUserIds, $lessonsUserIds);
    }

    /** @test */
    public function group_join_lesson_pivot_data_200(): void
    {
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200)->json();

        $this->assertEquals($data['count'], 4); // Only Active users

        $lessonsUserIds = $this->lesson->students()->get();
        for ($i = 0; $i < count($lessonsUserIds); $i++) {
            $this->assertEquals($lessonsUserIds[$i]->pivot->group_id, $this->group->id);
            $this->assertEquals($lessonsUserIds[$i]->pivot->group_name, $this->group->name);
        }

    }

    /** @test */
    public function group_sync_lesson_200(): void
    {
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 4); // Only Active users

        // One user got disabled
        $this->group->users()->whereNull('discharged_at')->first()->update(['discharged_at' => now()]);

        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 3); // Only Active users

    }

    /** @test */
    public function add_2_groups_200(): void
    {

        $group2 = Group::factory()->create();
        GroupUsers::factory()->group($group2)->count(2)->create();
        GroupUsers::factory()->group($group2)->discharged()->count(1)->create();

        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 4);

        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group2->id])->assertStatus(200);
        $this->assertEquals($data['count'], 2);

        $this->assertEquals($this->lesson->students()->count(), 6);
    }

}