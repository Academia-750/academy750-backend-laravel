<?php

namespace Tests\Feature\Oppositions\v1\getOppositions;

use App\Models\Opposition;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class FilterOppositionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_filter_oppositions_by_name(): void
    {

        Opposition::factory()->create([
            'name' => 'Temas de reflexion'
        ]);
        Opposition::factory()->create([
            'name' => 'Aplicacion de estrategias'
        ]);
        Opposition::factory()->create([
            'name' => 'Conocimientos tacticos'
        ]);

        $url = route('api.v1.oppositions.index'). "?&filter[name]=Aplicacion";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee('Aplicacion de estrategias')
            ->assertDontSee('Temas de reflexion')
            ->assertDontSee('Conocimientos tacticos');
    }

    /** @test */
    public function can_filter_oppositions_by_period(): void
    {

        Opposition::factory()->create([
            'period' => '2022-exam-C'
        ]);
        Opposition::factory()->create([
            'period' => '2022-exam-X'
        ]);
        Opposition::factory()->create([
            'period' => '2022-exam-III'
        ]);

        $url = route('api.v1.oppositions.index'). "?&filter[period]=2022-exam-III";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee('2022-exam-III')
            ->assertDontSee('2022-exam-C')
            ->assertDontSee('2022-exam-X');
    }

    /** @test */
    public function can_filter_students_by_created_at(): void
    {

        $createdAt1 = Carbon::now()->addDay();
        $createdAt4 = Carbon::now()->addDays(3);
        $createdAt3 = Carbon::now()->addDays(12);
        $createdAt2 = Carbon::now()->addDays(25);

        $opposition1 = Opposition::factory()->create([
            'created_at' => $createdAt1
        ]);

        $opposition2 = Opposition::factory()->create([
            'created_at' => $createdAt2
        ]);

        $opposition3 = Opposition::factory()->create([
            'created_at' => $createdAt3
        ]);

        $opposition4 = Opposition::factory()->create([
            'created_at' => $createdAt4
        ]);

        $url = route('api.v1.oppositions.index'). "?filter[created-at]={$createdAt2->format('Y-m-d')}";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee($opposition2->created_at->format('Y-m-d h:m:s'))
            ->assertDontSee($opposition1->created_at->format('Y-m-d h:m:s'))
            ->assertDontSee($opposition3->created_at->format('Y-m-d h:m:s'))
            ->assertDontSee($opposition4->created_at->format('Y-m-d h:m:s'));
    }

}
