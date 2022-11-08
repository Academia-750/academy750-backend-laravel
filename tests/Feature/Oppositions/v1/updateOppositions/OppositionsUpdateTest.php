<?php

namespace Tests\Feature\Oppositions\v1\updateOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class OppositionsUpdateTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_update_oppositions(): void
    {
        $opposition = Opposition::factory()->create();

        $data = [
            'name' => 'Opposition 1',
            'period' => '2022-exam-III'
        ];

        $url = route('api.v1.oppositions.update', [ 'opposition' => $opposition->getRouteKey() ] );

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('oppositions', [
            'name' => $data['name'],
            'period' => $data['period']
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'oppositions',
                'id' => $opposition->getRouteKey(),
                'attributes' => [
                    'name' => $data['name'],
                    'period' => $data['period'],
                    'is_visible' => $opposition->is_visible,
                    "created_at" => $opposition->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_only_name(): void
    {
        $opposition = Opposition::factory()->create();

        $data = [
            'name' => 'Opposition 1',
            //'period' => '2022-exam-III'
        ];

        $url = route('api.v1.oppositions.update', [ 'opposition' => $opposition->getRouteKey() ] );

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('oppositions', [
            'name' => $data['name']
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'oppositions',
                'id' => $opposition->getRouteKey(),
                'attributes' => [
                    'name' => $data['name'],
                    'period' => $opposition->period,
                    'is_visible' => $opposition->is_visible,
                    "created_at" => $opposition->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_only_period(): void
    {
        $opposition = Opposition::factory()->create();

        $data = [
            //'name' => 'Opposition 1',
            'period' => '2022-exam-III'
        ];

        $url = route('api.v1.oppositions.update', [ 'opposition' => $opposition->getRouteKey() ] );

        $response = $this->patchJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseHas('oppositions', [
            'period' => $data['period']
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'oppositions',
                'id' => $opposition->getRouteKey(),
                'attributes' => [
                    'name' => $opposition->name,
                    'period' => $data['period'],
                    'is_visible' => $opposition->is_visible,
                    "created_at" => $opposition->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }


}
