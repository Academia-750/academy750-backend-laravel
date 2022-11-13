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
        $opposition1 = Opposition::factory()->create([
            'name' => 'A'
        ]);
        $opposition2 = Opposition::factory()->create([
            'name' => 'B'
        ]);
        $opposition3 = Opposition::factory()->create([
            'name' => 'C'
        ]);

        $url = route('api.v1.oppositions.index'). '?sort=name';

        $response = $this->getJson($url);

        // asserts...

        $response->assertOk();

        $response->assertJsonCount(3, 'data');

        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'oppositions',
                    'id' => (string) $opposition1->getRouteKey(),
                    'attributes' => [
                        'name' => $opposition1->name,
                        'period' => $opposition1->period,
                        'is_available' => $opposition1->is_available,
                        "created_at" => $opposition1->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => []
                ],
                [
                    'type' => 'oppositions',
                    'id' => (string) $opposition2->getRouteKey(),
                    'attributes' => [
                        'name' => $opposition2->name,
                        'period' => $opposition2->period,
                        'is_available' => $opposition2->is_available,
                        "created_at" => $opposition2->created_at->format('Y-m-d h:m:s')
                    ],
                    'relationships' => []
                ],
                [
                    'type' => 'oppositions',
                    'id' => (string) $opposition3->getRouteKey(),
                    'attributes' => [
                        'name' => $opposition3->name,
                        'period' => $opposition3->period,
                        'is_available' => $opposition3->is_available,
                        "created_at" => $opposition3->created_at->format('Y-m-d h:m:s')
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
                    'is_available' => $opposition->is_available,
                    "created_at" => $opposition->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }
}
