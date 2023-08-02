<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class EditLessonTest extends TestCase
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

        $this->lesson = Lesson::factory()->create();

    }


    /** @test */
    public function not_route_405(): void
    {
        Auth::logout();
        $this->put("api/v1/lesson")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->put("api/v1/lesson/{$this->lesson->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->put("api/v1/lesson/{$this->lesson->id}")->assertStatus(403);
    }

    /** @test */
    public function not_found_404(): void
    {
        $this->put("api/v1/lesson/99")->assertStatus(404);
    }

    /** @test */
    public function wrong_params_422(): void
    {

        $this->put("api/v1/lesson/{$this->lesson->id}", ['name' => []])->assertStatus(422); // Wrong format
        // Rest of name are checked in specific test case

        $this->put("api/v1/lesson/{$this->lesson->id}", ['date' => null])->assertStatus(422); // Empty param
        $this->put("api/v1/lesson/{$this->lesson->id}", ['date' => "adasdasd"])->assertStatus(422); // Wrong format
        $this->put("api/v1/lesson/{$this->lesson->id}", ['date' => '2020-13-43'])->assertStatus(422); // Invalid date


        $this->put("api/v1/lesson/{$this->lesson->id}", ['start_time' => '08:00'])->assertStatus(422); // Only Start Time
        $this->put("api/v1/lesson/{$this->lesson->id}", ['start_time' => null, 'end_time' => '12:00'])->assertStatus(422); // Empty param
        $this->put("api/v1/lesson/{$this->lesson->id}", ['start_time' => "adasdasd", 'end_time' => '12:00'])->assertStatus(422); // Wrong format
        $this->put("api/v1/lesson/{$this->lesson->id}", ['start_time' => "25:00", 'end_time' => '12:00'])->assertStatus(422); // Wrong Date

        $this->put("api/v1/lesson/{$this->lesson->id}", ['end_time' => '08:00'])->assertStatus(422); // Only End Time
        $this->put("api/v1/lesson/{$this->lesson->id}", ['end_time' => null, 'start_time' => '08:00'])->assertStatus(422); // Empty param
        $this->put("api/v1/lesson/{$this->lesson->id}", ['end_time' => "adasdasd", 'start_time' => '08:00'])->assertStatus(422); // Wrong format
        $this->put("api/v1/lesson/{$this->lesson->id}", ['end_time' => "25:00", 'start_time' => '08:00'])->assertStatus(422); // Wrong Date
        $this->put("api/v1/lesson/{$this->lesson->id}", ['end_time' => "10:67", 'start_time' => '08:00'])->assertStatus(422); // Wrong Date

        $this->put("api/v1/lesson/{$this->lesson->id}", ['end_time' => "10:00", 'start_time' => "11:00"])->assertStatus(422); // Must be after


        $this->put("api/v1/lesson/{$this->lesson->id}", ['description' => null])->assertStatus(422); // Not a string
        $this->put("api/v1/lesson/{$this->lesson->id}", ['description' => '11'])->assertStatus(422); // Too short
        $this->put("api/v1/lesson/{$this->lesson->id}", ['description' => $this->faker->text(1010)])->assertStatus(422); // Too Long

        $this->put("api/v1/lesson/{$this->lesson->id}", ['is_online' => null])->assertStatus(422); // Wrong Value

        $this->put("api/v1/lesson/{$this->lesson->id}", ['url' => null])->assertStatus(422); // Not a string
        $this->put("api/v1/lesson/{$this->lesson->id}", ['url' => 'Not an url'])->assertStatus(422); // Not an url

    }


    /** @test */
    public function lesson_name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->put("api/v1/lesson/{$this->lesson->id}", ['name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }
    /** @test */
    public function edit_lesson_name_200(): void
    {
        $value = $this->faker->regexify('[a-zA-Z\s_-]{5,20}');
        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['name' => $value])->assertStatus(200)->json();

        $this->assertEquals($data['result']['name'], $value);

    }

    /** @test */
    public function edit_lesson_description_200(): void
    {
        $value = $this->faker->regexify('[a-zA-Z\s_-]{5,20}');
        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['description' => $value])->assertStatus(200)->json();

        $this->assertEquals($data['result']['description'], $value);
    }


    /** @test */
    public function edit_lesson_url_200(): void
    {
        $value = $this->faker->url();
        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['url' => $value])->assertStatus(200)->json();

        $this->assertEquals($data['result']['url'], $value);
    }

    /** @test */
    public function cant_edit_active_lesson_403(): void
    {
        $this->lesson->is_active = true;
        $this->lesson->save();


        $this->put("api/v1/lesson/{$this->lesson->id}", ['name' => 'Not important '])->assertStatus(403);

    }

    /** @test */
    public function edit_lesson_date_200(): void
    {
        $value = now()->add($this->faker->randomDigit(), 'days')->startOf('day')->toISOString();

        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['date' => $value])->assertStatus(200)->json();

        $this->assertEquals($data['result']['date'], $value);
    }
    /** @test */
    public function edit_is_online_date_200(): void
    {

        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['is_online' => true])->assertStatus(200)->json();

        $this->assertEquals($data['result']['is_online'], 1);

        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['is_online' => false])->assertStatus(200)->json();

        $this->assertEquals($data['result']['is_online'], 0);


    }

    /** @test */
    public function edit_lesson_start_end_time_200(): void
    {
        $start = now()->format('H:i');
        $end = now()->add(2, 'hours')->format('H:i');


        $data = $this->put("api/v1/lesson/{$this->lesson->id}", ['start_time' => $start, 'end_time' => $end])->assertStatus(200)->json();

        $this->assertEquals($data['result']['start_time'], $start);
        $this->assertEquals($data['result']['end_time'], $end);
    }

    /** @test */
    public function edit_all_at_once_200(): void
    {
        $body = [
            'date' => $this->faker()->date(),
            'description' => $this->faker->text(200),
            'url' => $this->faker->url(),
            'is_online' => true,
            'start_time' => now()->format('H:i'),
            'end_time' => now()->add(2, 'hours')->format('H:i')
        ];


        $this->put("api/v1/lesson/{$this->lesson->id}", $body)->assertStatus(200)->json();
    }

}