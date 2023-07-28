<?php
namespace Database\Seeders\trait;

use App\Models\Opposition;

trait OppositionsHelpersTrait
{
    public function syncOppositions ($model): void {
        $oppositions = Opposition::all();

        $oppositionsRandom = [];

        foreach ( range(1, random_int(2,3)) as $n) {
            $oppositionsRandom[] = $oppositions->random()->getKey();
        }

        $model->oppositions()->sync($oppositionsRandom);
    }
}
