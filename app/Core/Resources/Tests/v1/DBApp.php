<?php
namespace App\Core\Resources\Tests\v1;

use App\Core\Resources\Tests\Services\QuestionsTestService;
use App\Core\Resources\Tests\Services\TestsService;
use App\Models\Opposition;
use App\Models\Question;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Models\TestType;
use Illuminate\Support\Facades\Auth;


class DBApp implements TestsInterface
{
    protected Test $model;

    public function __construct(Test $test ){
        $this->model = $test;
    }

    public function get_tests_unresolved(){
        return Auth::user()?->tests()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function fetch_unresolved_test( $test ): \App\Models\Test{

        $testQuery = Auth::user()->tests()->firstWhere('id', '=', $test->getRouteKey());

        if (!$testQuery) {
            abort(404);
        }

        return $this->model->applyIncludes()->find($testQuery->getRouteKey());
    }

    public function create_a_quiz( $request ): Test
    {
        $opposition = Opposition::findOrFail($request->get('opposition_id'));
        $testType = TestType::findOrFail($request->get('test_type_id'));
        $user = Auth::user();

        $questionnaire = TestsService::createTest([
            "number_of_questions_requested" => (int) $request->get('count_questions_for_test'),
            "opposition_id" => $opposition->getRouteKey(),
            "test_type_id" => $testType->getRouteKey(),
            "user_id" => $user?->getRouteKey()
        ]);

        TestsService::registerTopicsAndSubtopicsByTest($questionnaire, $request->get('topics_id'), $opposition);

        QuestionsTestService::buildQuestionsTest(
            (int) $request->get('count_questions_for_test'),
            $testType,
            $user,
            $questionnaire
        );

        return $questionnaire;
    }
}
