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


class LessonGroupsAddTest extends TestCase
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
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => -23])->assertStatus(422); // No negative
        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => 22.22])->assertStatus(422); // No decimals
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

        // sync group
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 3); // Only Active users

    }


    /** @test */
    public function group_sync_lesson_with_attendees_200(): void
    {
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);

        $this->assertEquals($data['count'], 4); // Only Active users

        $this->lesson->students()->newPivotStatement()->update(['will_join' => true]);

        // We add a new user to the group
        $this->group->users()->save(GroupUsers::factory()->create(['group_id' => $this->group->id]));

        // sync group
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 5); // Only Active users

        $willJoinCount = $this->lesson->students()->wherePivot('will_join', true)->count();

        $this->assertEquals($willJoinCount, 4); // The 4 before have still will join marked as true


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


    /** @test */
    public function add_group_dont_override_single_students_200(): void
    {

        /**
         * A user added to a lesson. If a group is added which contains an existing user the user info is not duplicated
         * or overridden
         */
        $groupStudent = $this->group->users()->first()->user()->first();
        $this->post("api/v1/lesson/{$this->lesson->id}/student", ['user_id' => $groupStudent->uuid])->assertStatus(200);

        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 3);

        $count = $this->lesson->students()->where('user_id', $groupStudent['id'])->count();
        $this->assertEquals($count, 1);
    }

    /** @test */
    public function add_group_dont_override_existing_users_from_other_groups_200(): void
    {

        $group2 = Group::factory()->create();
        $group2->users()->saveMany($this->group->users()->get()->all());

        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $group2->id])->assertStatus(200);

        //
        $data = $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->assertEquals($data['count'], 0);

        $count = $this->lesson->students()->wherePivot('group_id', $this->group->id)->count();
        $this->assertEquals($count, 0);
    }


    /** @test */
    public function groups_with_same_name_dont_get_override_200(): void
    {
        $lesson2 = Lesson::factory()->create(['name' => $this->lesson->name]);

        $this->post("api/v1/lesson/{$this->lesson->id}/group", ['group_id' => $this->group->id])->assertStatus(200);
        $this->post("api/v1/lesson/{$lesson2->id}/group", ['group_id' => $this->group->id])->assertStatus(200);

        $this->assertEquals($this->lesson->students()->count(), 4);
        $this->assertEquals($lesson2->students()->count(), 4);
    }

}
