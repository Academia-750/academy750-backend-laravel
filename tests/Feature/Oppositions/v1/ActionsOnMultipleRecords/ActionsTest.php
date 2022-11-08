<?php

namespace Tests\Feature\Oppositions\v1\ActionsOnMultipleRecords;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class ActionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_delete_multiple_oppositions(): void
    {
        $opposition1 = Opposition::factory()->create();

        $opposition2 = Opposition::factory()->create();

        $opposition3 = Opposition::factory()->create();

        $url = route('api.v1.oppositions.actions-on-multiple-records');

        $data = [
            "action" => "delete",
            "oppositions" => [
                $opposition1->getRouteKey(),
                $opposition2->getRouteKey(),
                $opposition3->getRouteKey(),
            ]
        ];

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseMissing('oppositions', $opposition1->toArray());
        $this->assertDatabaseMissing('oppositions', $opposition2->toArray());
        $this->assertDatabaseMissing('oppositions', $opposition3->toArray());
    }
}
