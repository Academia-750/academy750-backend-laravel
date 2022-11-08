<?php

namespace Tests\Feature\Oppositions\v1\createOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class OppositionsCreateTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_create_oppositions(): void
    {
        $opposition = Opposition::factory()->make()->toArray();

        $data = [
            'name' => $opposition['name'],
            'period' => $opposition['period'],
        ];

        $url = route('api.v1.oppositions.create');

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertCreated();

        $this->assertDatabaseHas('oppositions', $data);
    }
}
