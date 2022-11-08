<?php

namespace Tests\Feature\Oppositions\v1\createOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class OppositionsCreateValidationTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function name_is_required(): void
    {
        $data = [
            //'name' => '',
            'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('oppositions', $data);

    }

    /** @test */
    public function name_must_not_be_a_longer_than_100_characters(): void
    {
        $data = [
            'name' => Str::random(101),
            'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('oppositions', $data);

    }


    /** @test */
    public function name_must_be_unique(): void
    {
        $nameOpposition = 'Conocimientos tacticos';

        Opposition::factory()->create([
            'name' => $nameOpposition
        ]);

        $data = [
            'name' => $nameOpposition,
            'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('oppositions', $data);

    }

    /** @test */
    public function period_is_required(): void
    {
        $data = [
            'name' => 'ABCDEF',
            //'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('period');

        $this->assertDatabaseMissing('oppositions', $data);

    }

    /** @test */
    public function period_must_not_be_a_longer_than_100_characters(): void
    {
        $data = [
            'name' => 'ABCDEF',
            'period' => Str::random(101)
        ];

        $url = route('api.v1.oppositions.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('period');

        $this->assertDatabaseMissing('oppositions', $data);

    }
}
