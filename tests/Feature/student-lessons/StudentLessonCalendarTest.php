<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class StudentLessonCalendarTest extends TestCase
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

        $this->user = User::factory()->student()->allowedTo(Permission::SEE_LESSONS)->create();


        // 4 Lessons, 2 active 2 online
        $this->lessons = Lesson::factory()
            ->sequence(['is_active' => true], ['is_active' => false])
            ->sequence(['is_online' => true], ['is_online' => false])
            ->sequence(function ($sequence) {
                return ['date' => now()->add($sequence->index, 'day')->startOfDay()];
            })
            ->withStudents($this->user)
            ->count(4)
            ->create(['url' => $this->faker()->url()]);

        $this->body = [
            'from' => now()->format('Y-m-d'),
            'to' => now()->add(4, 'day')->format('Y-m-d'),
        ];

        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/calendar?")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/student-lessons/calendar?")->assertStatus(403);
    }




    /** @test */
    public function wrong_parameters_422(): void
    {

        // From - To
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['from' => now()]))->assertStatus(422); //  Missing To
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['to' => now()]))->assertStatus(422); //  Missing From
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['from' => now(), 'to' => 'not-a-valid-date']))->assertStatus(422); //  To no valid
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['from' => 'not-a-valid-date', 'to' => now()]))->assertStatus(422); //  To no valid
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['from' => now(), 'to' => now()->add(-1, 'day')]))->assertStatus(422); //  To Before From

        // Content
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['from' => now(), 'to' => now()->add(1, 'day'), 'content' => null]))->assertStatus(422); //  Content not an string

    }

    /** @test */
    public function maximum_range_allowed_422(): void
    {
        $this->get("api/v1/student-lessons/calendar?" . Arr::query(['from' => now(), 'to' => now()->add(90 + 1, 'day'), 'content' => null]))->assertStatus(422); //  Content not an string
    }

    /** @test */
    public function search_one_day_200(): void
    {

        // will included the start to the end of the day
        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query([
            'from' => now()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
    }


    /** @test */
    public function get_all_lessons_200(): void
    {

        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query($this->body))->assertStatus(200);
        $this->assertEquals(count($data['results']), 4);
        $this->assertEquals($data['total'], 4);
    }

    /** @test */
    public function student_lesson_content_200(): void
    {

        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query($this->body))->assertStatus(200);
        $lesson = $data['results'][0];

        $this->assertEquals($lesson['id'], $this->lessons[0]->id);
        $this->assertEquals($lesson['name'], $this->lessons[0]->name);
        $this->assertEquals($lesson['description'], $this->lessons[0]->description);
        $this->assertEquals($lesson['is_online'], $this->lessons[0]->is_online);
        $this->assertEquals($lesson['is_active'], $this->lessons[0]->is_active);
        $this->assertEquals($lesson['date'], $this->lessons[0]->date);
        $this->assertEquals($lesson['start_time'], $this->lessons[0]->start_time);
        $this->assertEquals($lesson['end_time'], $this->lessons[0]->end_time);
        $this->assertEquals($lesson['will_join'], false);
        $this->assertEquals($lesson['color'], null);
        $this->assertEquals($lesson['user_id'], $this->user->id);
        // URL is hidden, there is specific API for users with permissions for it
        $this->assertFalse(isset($lesson['url']));



    }

    /** @test */
    public function has_joined_lesson_content_200(): void
    {

        $this->lessons[0]->students()->updateExistingPivot($this->user->id, ['will_join' => true]);

        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query($this->body))->assertStatus(200);
        $lesson = $data['results'][0];

        $this->assertEquals($lesson['id'], $this->lessons[0]->id);
        $this->assertEquals($lesson['will_join'], true);

    }

    /** @test */
    public function filter_from_to_200(): void
    {

        // Check every day shall be only 1 lesson
        array_map(function ($index) {
            $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query([
                'from' => now()->add($index, 'days')->toISOString(),
                'to' => now()->add($index, 'days')->endOfDay()->toISOString()
            ]))->assertStatus(200);

            $this->assertEquals($data['total'], 1);
            $this->assertEquals($data['results'][0]['id'], $this->lessons[$index]['id']);

        }, [0, 1, 2, 3]);
    }

    /** @test */
    public function content_200(): void
    {

        // Try Name 0
        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query([
            ...$this->body,
            'content' => substr($this->lessons[0]->name, 0, 3)
        ]))->assertStatus(200);


        $this->assertEquals($data['results'][0]['id'], $this->lessons[0]['id']);
        $this->assertEquals($data['total'], 1);

        // Try Name 1
        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query([
            ...$this->body,
            'content' => substr($this->lessons[1]->name, 1, 3)
        ]))->assertStatus(200);

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['id'], $this->lessons[1]['id']);
    }

    /** @test */
    public function group_color_200(): void
    {
        $group = Group::factory()->create();
        GroupUsers::factory()->group($group)->count(3)->create();
        $group2 = Group::factory()->create();
        GroupUsers::factory()->group($group2)->count(2)->create();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post("api/v1/lesson/{$this->lessons[0]->id}/group", ['group_id' => $group->id])->assertStatus(200);
        $this->actingAs($admin)->post("api/v1/lesson/{$this->lessons[0]->id}/group", ['group_id' => $group2->id])->assertStatus(200);


        $data = $this->actingAs($this->user)->get("api/v1/student-lessons/calendar?" . Arr::query($this->body))->assertStatus(200);

        $this->assertEquals($data['results'][0]['color'], $group->color); // People from group 1 are more number

        // Other groups have no color yet
        $this->assertNull($data['results'][1]['color']);

    }

    /** @test */
    public function can_not_see_other_lessons_200(): void
    {
        $this->lessons = Lesson::factory()
            ->withStudents(User::factory()->student()->create())
            ->count(2)
            ->create();

        $data = $this->get("api/v1/student-lessons/calendar?" . Arr::query($this->body))->assertStatus(200);
        $this->assertEquals($data['total'], 4);

        $user_ids = $this->map($data['results'], 'user_id');
        $this->assertEquals($user_ids, [$this->user->id, $this->user->id, $this->user->id, $this->user->id]);
    }
}