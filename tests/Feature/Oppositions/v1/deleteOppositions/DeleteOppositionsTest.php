<?php

namespace Tests\Feature\Oppositions\v1\deleteOppositions;

use App\Core\Services\UuidGeneratorService;
use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class DeleteOppositionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_delete_oppositions(): void
    {
        $opposition = Opposition::factory()->create();

        $url = route('api.v1.oppositions.delete', [ 'opposition' => $opposition->getRouteKey() ]);

        $response = $this->deleteJson($url);

        // asserts...

        $response->assertNoContent();

        $this->assertDatabaseMissing('oppositions', $opposition->toArray());
    }


    /** @test */
    public function cannot_a_delete_a_unknown_opposition(): void
    {
        $url = route('api.v1.users.delete', [ 'user' => UuidGeneratorService::getUUIDUnique(Opposition::class) ]);

        $response = $this->deleteJson($url);

        // asserts...

        $response->assertNotFound();
    }
}
