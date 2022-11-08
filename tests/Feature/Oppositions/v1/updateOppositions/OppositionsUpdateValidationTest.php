<?php

namespace Tests\Feature\Oppositions\v1\updateOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class OppositionsUpdateValidationTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function name_must_not_be_a_longer_than_100_characters(): void
    {
        $opposition = Opposition::factory()->create();

        $data = [
            'name' => Str::random(101),
            //'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.update', [ 'opposition' => $opposition->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

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

        $opposition = Opposition::factory()->create();

        $data = [
            'name' => $nameOpposition,
            //'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.update', [ 'opposition' => $opposition->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name');

    }

    /** @test */
    public function period_must_not_be_a_longer_than_100_characters(): void
    {
        $opposition = Opposition::factory()->create();

        $data = [
            'period' => Str::random(101),
            //'period' => '2022-exam-I'
        ];

        $url = route('api.v1.oppositions.update', [ 'opposition' => $opposition->getRouteKey() ]);

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertUnprocessable()->assertJsonValidationErrorFor('period');

        $this->assertDatabaseMissing('oppositions', $data);

    }
}
