<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class CreateLessonTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $body;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->body = [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'date' => now()->startOf('day')->toISOString(),
            'start_time' => now()->setTime(10, 0)->format('H:i'),
            'end_time' => now()->setTime(11, 0)->format('H:i'),
        ];

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/lesson")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/lesson")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {

        $this->post("api/v1/lesson", [])->assertStatus(422);
        $this->post("api/v1/lesson", [...$this->body, 'name' => ''])->assertStatus(422); // Empty param
        $this->post("api/v1/lesson", [...$this->body, 'name' => []])->assertStatus(422); // Wrong format
        // Rest of name are checked in specific test case

        $this->post("api/v1/lesson", [...$this->body, 'date' => null])->assertStatus(422); // Empty param
        $this->post("api/v1/lesson", [...$this->body, 'date' => "adasdasd"])->assertStatus(422); // Wrong format
        $this->post("api/v1/lesson", [...$this->body, 'date' => '2020-13-43'])->assertStatus(422); // Invalid date


        $this->post("api/v1/lesson", [...$this->body, 'start_time' => null])->assertStatus(422); // Empty param
        $this->post("api/v1/lesson", [...$this->body, 'start_time' => "adasdasd"])->assertStatus(422); // Wrong format
        $this->post("api/v1/lesson", [...$this->body, 'start_time' => "25:00"])->assertStatus(422); // Wrong Date
        $this->post("api/v1/lesson", [...$this->body, 'end_time' => "10:67"])->assertStatus(422); // Wrong Date

        $this->post("api/v1/lesson", [...$this->body, 'end_time' => null])->assertStatus(422); // Empty param
        $this->post("api/v1/lesson", [...$this->body, 'end_time' => "adasdasd"])->assertStatus(422); // Wrong format
        $this->post("api/v1/lesson", [...$this->body, 'end_time' => "25:00"])->assertStatus(422); // Wrong Date
        $this->post("api/v1/lesson", [...$this->body, 'end_time' => "10:67"])->assertStatus(422); // Wrong Date

        $this->post("api/v1/lesson", [...$this->body, 'end_time' => "10:00", 'start_time' => "11:00"])->assertStatus(422); // Must be after
    }


    /** @test */
    public function lesson_name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->post("api/v1/lesson", [...$this->body, 'name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }
    /** @test */
    public function create_lesson_200(): void
    {

        $data = $this->post("api/v1/lesson", $this->body)->assertStatus(200)->json();

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['name'], $this->body['name']);
        $this->assertEquals($data['result']['date'], $this->body['date']);
        $this->assertEquals($data['result']['start_time'], $this->body['start_time']);
        $this->assertEquals($data['result']['end_time'], $this->body['end_time']);
        // Default values
        $this->assertEquals($data['result']['description'], '');
        $this->assertEquals($data['result']['is_online'], false);
        $this->assertEquals($data['result']['is_active'], false);
        $this->assertEquals($data['result']['url'], '');
    }

}