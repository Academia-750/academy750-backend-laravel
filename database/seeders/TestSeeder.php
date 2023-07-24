<?php

namespace Database\Seeders;

use App\Core\Resources\Tests\Services\QuestionsTestService;
use App\Core\Resources\Tests\Services\TestsService;
use App\Models\Opposition;
use App\Models\Role;
use App\Models\Test;
use App\Models\TestType;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->generateTestsFaker();
    }

    public function generateTestsFaker (): void {
        foreach ( range(1, 4) as $n ) {
            $opposition = Opposition::all()->random();
            $testTypes = ['test', 'card_memory'];
            $user = User::query()->firstWhere('dni', '=', '14071663X');

            $questionnaire = TestsService::createTest([
                "number_of_questions_requested" => "25",
                "test_type" => $testTypes[random_int(0,1)],
                "opposition_id" => $opposition->getKey(),
                "user_id" => $user?->getKey()
            ]);

            $topicsSelected = Topic::all()->random(5)->pluck('id')->toArray();

            TestsService::registerTopicsAndSubtopicsByTest($questionnaire, $topicsSelected, $opposition);

            QuestionsTestService::buildQuestionsTest( $topicsSelected, 25, $opposition, $user, $questionnaire );

        }
    }


}
