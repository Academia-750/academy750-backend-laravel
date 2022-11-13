<?php

namespace Database\Seeders;

use App\Models\Opposition;
use App\Models\Role;
use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $numberOfQuestions = [10, 25, 50, 100];
        $testTypes = TestType::all();
        $oppositions = Opposition::all();
        $students = Role::query()->firstWhere('name', '=', 'student')->users;

        foreach ( range(1, 25) as $n ) {
            Test::query()->create([
                'number_of_questions' => $numberOfQuestions[ random_int(0,3) ],
                'test_result' => random_int(0.0, 10.0),
                'is_solved_test' => 'no',
                'test_type_id' => $testTypes->random()->getRouteKey(),
                'opposition_id' => $oppositions->random()->getRouteKey(),
                'user_id' => $students->random()->getRouteKey()
            ]);
        }
    }
}
