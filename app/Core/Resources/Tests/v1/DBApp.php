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
        return Auth::user()?->tests()->where('test_type', '=', 'test')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_cards_memory()
    {
        return Auth::user()?->tests()->where('test_type', '=', 'card_memory')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function fetch_unresolved_test( $test ): \App\Models\Test{

        $testQuery = Auth::user()->tests()->where('test_type', '=', 'test')->where('id', '=', $test->getRouteKey())->first();

        if (!$testQuery) {
            abort(404);
        }

        return $this->model->find($testQuery->getRouteKey())->questions()->with('answers_by_test')->jsonPaginate();
    }

    public function fetch_card_memory( $test ): \App\Models\Test{

        $testQuery = Auth::user()->tests()->firstWhere('id', '=', $test->getRouteKey());

        if (!$testQuery) {
            abort(404);
        }

        return $this->model->find($testQuery->getRouteKey())->questions()->with('answers')->jsonPaginate();
    }

    public function create_a_quiz( $request ): Test
    {
        $opposition = Opposition::findOrFail($request->get('opposition_id'));
        $testType = $request->get('test_type'); // test || card_memory
        $user = Auth::user();

        if (!$user) {
            abort(404);
        }

        $questionnaire = TestsService::createTest([
            "number_of_questions_requested" => (int) $request->get('count_questions_for_test'),
            "opposition_id" => $opposition->getRouteKey(),
            "test_type" => $testType,
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
