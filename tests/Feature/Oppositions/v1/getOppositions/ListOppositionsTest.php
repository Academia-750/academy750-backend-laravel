<?php

namespace Tests\Feature\Oppositions\v1\getOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class ListOppositionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_fetch_all_oppositions(): void
    {
        $oppositions = Opposition::factory()->count(3)->create();

        $url = route('api.v1.oppositions.index');

        $response = $this->getJson($url);

        // asserts...

        $response->assertOk();

        $response->assertJsonCount(3, 'data');

        $response->assertJson([
            'data' => [
                [
                    'type' => 'oppositions',
                    'id' => (string) $oppositions[0]->getRouteKey(),
                    'attributes' => [
                        'name' => $oppositions[0]->name,
                        'period' => $oppositions[0]->period,
                        'is_visible' => $oppositions[0]->is_visible,
                        "created_at" => $oppositions[0]->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => []
                ],
                [
                    'type' => 'oppositions',
                    'id' => (string) $oppositions[1]->getRouteKey(),
                    'attributes' => [
                        'name' => $oppositions[1]->name,
                        'period' => $oppositions[1]->period,
                        'is_visible' => $oppositions[1]->is_visible,
                        "created_at" => $oppositions[1]->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => []
                ],
                [
                    'type' => 'oppositions',
                    'id' => (string) $oppositions[2]->getRouteKey(),
                    'attributes' => [
                        'name' => $oppositions[2]->name,
                        'period' => $oppositions[2]->period,
                        'is_visible' => $oppositions[2]->is_visible,
                        "created_at" => $oppositions[2]->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => []
                ],
            ]
        ]);
    }

    /** @test */
    public function can_fetch_a_opposition(): void
    {
        $opposition = Opposition::factory()->create();

        $url = route('api.v1.oppositions.read', compact('opposition'));

        $response = $this->getJson($url);

        // asserts...

        $response->assertOk();

        $response->assertExactJson([
            'data' => [
                'type' => 'oppositions',
                'id' => (string) $opposition->getRouteKey(),
                'attributes' => [
                    'name' => $opposition->name,
                    'period' => $opposition->period,
                    'is_visible' => $opposition->is_visible,
                    "created_at" => $opposition->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }
}
