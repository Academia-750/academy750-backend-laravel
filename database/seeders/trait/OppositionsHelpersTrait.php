<?php
namespace Database\Seeders\trait;

use App\Models\Opposition;

trait OppositionsHelpersTrait
{
    public function syncOppositions ($model): void {
        $oppositions = Opposition::all();

        $oppositionsRandom = [];

        foreach ( range(3, random_int(5,15)) as $n) {
            $oppositionsRandom[] = $oppositions->random()->getRouteKey();
        }

        $model->oppositions()->sync($oppositionsRandom);
    }
}
