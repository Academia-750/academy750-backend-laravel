<?php

namespace Tests\Feature\Oppositions\v1\getOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class SortOppositionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_sort_oppositions_by_name_ascending(): void
    {
        Opposition::factory()->create([
            'name' => 'Conocimientos'
        ]);

        Opposition::factory()->create([
            'name' => 'Aplicacion'
        ]);

        Opposition::factory()->create([
            'name' => 'Temas de reflexion'
        ]);

        $url = route('api.v1.oppositions.index'). "?sort=name";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Aplicacion',
                'Conocimientos',
                'Temas de reflexion',
            ]);
    }

    /** @test */
    public function can_sort_oppositions_by_name_descending(): void
    {
        Opposition::factory()->create([
            'name' => 'Conocimientos'
        ]);

        Opposition::factory()->create([
            'name' => 'Aplicacion'
        ]);

        Opposition::factory()->create([
            'name' => 'Temas de reflexion'
        ]);

        $url = route('api.v1.oppositions.index'). "?sort=-name";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Temas de reflexion',
                'Conocimientos',
                'Aplicacion',
            ]);
    }

    /** @test */
    public function can_sort_oppositions_by_period_ascending(): void
    {
        Opposition::factory()->create([
            'period' => '2022 - Conocimientos'
        ]);

        Opposition::factory()->create([
            'period' => '2021 - Aplicacion'
        ]);

        Opposition::factory()->create([
            'period' => '2022 - Temas de reflexion'
        ]);

        $url = route('api.v1.oppositions.index'). "?sort=period";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                '2021 - Aplicacion',
                '2022 - Conocimientos',
                '2022 - Temas de reflexion',
            ]);
    }

    /** @test */
    public function can_sort_oppositions_by_period_descending(): void
    {
        Opposition::factory()->create([
            'period' => '2022 - Conocimientos'
        ]);

        Opposition::factory()->create([
            'period' => '2021 - Aplicacion'
        ]);

        Opposition::factory()->create([
            'period' => '2022 - Temas de reflexion'
        ]);

        $url = route('api.v1.oppositions.index'). "?sort=-period";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                '2022 - Temas de reflexion',
                '2022 - Conocimientos',
                '2021 - Aplicacion',
            ]);
    }


    /** @test */
    public function can_sort_students_by_created_at_ascending(): void
    {
        $createdAt1 = Carbon::now()->addDay();
        $createdAt2 = Carbon::now()->addDays(20);
        $createdAt3 = Carbon::now()->addDays(10);
        $createdAt4 = Carbon::now()->addDays(3);

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


        $url = route('api.v1.oppositions.index'). "?sort=created-at";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                $opposition1->created_at->format('Y-m-d h:m:s'),
                $opposition4->created_at->format('Y-m-d h:m:s'),
                $opposition3->created_at->format('Y-m-d h:m:s'),
                $opposition2->created_at->format('Y-m-d h:m:s'),
            ]);
    }

    /** @test */
    public function can_sort_students_by_created_at_descending(): void
    {
        $createdAt1 = Carbon::now()->addDay();
        $createdAt2 = Carbon::now()->addDays(20);
        $createdAt3 = Carbon::now()->addDays(10);
        $createdAt4 = Carbon::now()->addDays(3);

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



        $url = route('api.v1.oppositions.index'). "?sort=-created-at";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                $opposition2->created_at->format('Y-m-d h:m:s'),
                $opposition3->created_at->format('Y-m-d h:m:s'),
                $opposition4->created_at->format('Y-m-d h:m:s'),
                $opposition1->created_at->format('Y-m-d h:m:s'),
            ]);
    }
}
