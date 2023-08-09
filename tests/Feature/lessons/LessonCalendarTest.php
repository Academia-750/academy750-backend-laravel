<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class LessonCalendarTest extends TestCase
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

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        /**
         * For query purposes we generate a lesson each day
         */
        $this->lessons = Lesson::factory()
            ->count(4)
            ->sequence(['is_active' => true], ['is_active' => false])
            ->sequence(['is_online' => true], ['is_online' => false])
            ->sequence(['is_online' => true], ['is_online' => false])
            ->sequence(function ($sequence) {
                return ['date' => now()->add($sequence->index, 'day')->startOfDay()];
            })
            ->create();

        $this->body = [
            'from' => now()->format('Y-m-d'),
            'to' => now()->add(4, 'day')->format('Y-m-d'),
        ];



    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson/calendar?")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/lesson/calendar?")->assertStatus(403);
    }


    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->get("api/v1/lesson/calendar?" . Arr::query(['from' => now()]))->assertStatus(422); //  Missing To
        $this->get("api/v1/lesson/calendar?" . Arr::query(['to' => now()]))->assertStatus(422); //  Missing From
        $this->get("api/v1/lesson/calendar?" . Arr::query(['from' => now(), 'to' => 'not-a-valid-date']))->assertStatus(422); //  To no valid
        $this->get("api/v1/lesson/calendar?" . Arr::query(['from' => 'not-a-valid-date', 'to' => now()]))->assertStatus(422); //  To no valid
        $this->get("api/v1/lesson/calendar?" . Arr::query(['from' => now(), 'to' => now()->add(-1, 'day')]))->assertStatus(422); //  To Before From

        // Content
        $this->get("api/v1/lesson/calendar?" . Arr::query(['from' => now(), 'to' => now()->add(1, 'day'), 'content' => null]))->assertStatus(422); //  Content not an string

    }

    /** @test */
    public function maximum_range_allowed_422(): void
    {
        $this->get("api/v1/lesson/calendar?" . Arr::query(['from' => now(), 'to' => now()->add(90 + 1, 'day'), 'content' => null]))->assertStatus(422); //  Content not an string
    }

    /** @test */
    public function search_one_day_200(): void
    {

        // will included the start to the end of the day
        $data = $this->get("api/v1/lesson/calendar?" . Arr::query([
            'from' => now()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]))->assertStatus(200)->json();

        $this->assertEquals($data['total'], 1);
    }


    /** @test */
    public function get_all_lessons_200(): void
    {

        $data = $this->get("api/v1/lesson/calendar?" . Arr::query($this->body))->assertStatus(200)->json();
        $this->assertEquals(count($data['results']), 4);
        $this->assertEquals($data['total'], 4);
    }

    /** @test */
    public function lesson_content_200(): void
    {

        $data = $this->get("api/v1/lesson/calendar?" . Arr::query($this->body))->assertStatus(200)->json();
        $lesson = $data['results'][0];

        $this->assertEquals($lesson['id'], $this->lessons[0]->id);
        $this->assertEquals($lesson['name'], $this->lessons[0]->name);
        $this->assertEquals($lesson['description'], $this->lessons[0]->description);
        $this->assertEquals($lesson['is_online'], $this->lessons[0]->is_online);
        $this->assertEquals($lesson['is_active'], $this->lessons[0]->is_active);
        $this->assertEquals($lesson['date'], $this->lessons[0]->date);
        $this->assertEquals($lesson['start_time'], $this->lessons[0]->start_time);
        $this->assertEquals($lesson['end_time'], $this->lessons[0]->end_time);
    }

    /** @test */
    public function filter_from_to_200(): void
    {

        // Check every day shall be only 1 lesson
        array_map(function ($index) {
            $data = $this->get("api/v1/lesson/calendar?" . Arr::query([
                'from' => now()->add($index, 'days')->toISOString(),
                'to' => now()->add($index, 'days')->endOfDay()->toISOString()
            ]))->assertStatus(200)->json();

            $this->assertEquals($data['total'], 1);
            $this->assertEquals($data['results'][0]['id'], $this->lessons[$index]['id']);

        }, [0, 1, 2, 3]);
    }

    /** @test */
    public function content_200(): void
    {

        // Try Name 0
        $data = $this->get("api/v1/lesson/calendar?" . Arr::query([
            ...$this->body,
            'content' => substr($this->lessons[0]->name, 0, 3)
        ]))->assertStatus(200)->json();


        $this->assertEquals($data['results'][0]['id'], $this->lessons[0]['id']);
        $this->assertEquals($data['total'], 1);

        // Try Name 1
        $data = $this->get("api/v1/lesson/calendar?" . Arr::query([
            ...$this->body,
            'content' => substr($this->lessons[1]->name, 1, 3)
        ]))->assertStatus(200)->json();

        $this->assertEquals($data['total'], 1);
        $this->assertEquals($data['results'][0]['id'], $this->lessons[1]['id']);

    }
}