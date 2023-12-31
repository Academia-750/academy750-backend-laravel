<?php
namespace App\Core\Resources\Tests\v1;

use App\Core\Resources\Tests\Services\QuestionsTestService;
use App\Core\Resources\Tests\Services\QueryParametersQuestionsForResolveTest;
use App\Core\Resources\Tests\Services\TestsService;
use App\Models\Answer;
use App\Models\Opposition;
use App\Models\Question;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DBApp implements TestsInterface
{
    protected Test $model;

    public function __construct(Test $test)
    {
        $this->model = $test;
    }

    public function get_tests_unresolved()
    {
        return Auth::user()?->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'no')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_cards_memory()
    {
        return Auth::user()?->tests()->where('test_type', '=', 'card_memory')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function fetch_unresolved_test($test)
    {

        $testQuery = Auth::user()
            ->tests()
            ->where('test_type', '=', 'test')
            ->where('id', '=', $test->getKey())
            ->first();

        if (!$testQuery) {
            abort(404);
        }

        return QueryParametersQuestionsForResolveTest::getQuestionsEloquentSortByIndexByTest($testQuery);
    }

    public function fetch_card_memory($test)
    {

        $testQuery = Auth::user()->tests()->where('test_type', '=', 'card_memory')->firstWhere('id', '=', $test->getKey());

        if (!$testQuery) {
            abort(404);
        }

        return Question::query()->whereIn('id', $testQuery->questions()->pluck('questions.id')->toArray())->jsonPaginate();
    }

    public function create_a_quiz($request)
    {
        try {

            $dataForTheBuildTest = TestsService::getDataToCreateTests($request);

            abort_if(!$dataForTheBuildTest['userAuth'], 404);

            $dataForTheBuildTest = TestsService::createTestReference($dataForTheBuildTest);

            TestsService::registerTopicsAndSubtopicsByTest(
                $dataForTheBuildTest
            );

            QuestionsTestService::buildQuestionsTest(
                $dataForTheBuildTest
            );
            return $dataForTheBuildTest['testRecordReferenceCreated'];
        } catch (\Exception $e) {
            abort(500, $e);
        }

    }

    public function resolve_a_question_of_test($request)
    {
        try {

            $test = Test::query()
                ->where('uuid', $request->get('test_id'))
                ->first();

            $question = Question::query()
                ->firstWhere('uuid', '=', $request->get('question_id'));

            $answer = Answer::query()
                ->where('uuid', $request->get('answer_id'))
                ->first();

            if ($request->get('answer_id')) {

                $stateQuestionAnswered = $answer->is_correct_answer === 'yes' ? 'correct' : 'wrong';

                $test->questions()->wherePivot('question_id', $question->getKey())->updateExistingPivot($question->getKey(), [
                    'answer_id' => $answer->getKey(),
                    'status_solved_question' => $stateQuestionAnswered,
                    /*'have_been_show_test' => $stateQuestionAnswered === 'wrong' ? 'no' : 'yes'*/
                ]);

            } else {
                $test->questions()->wherePivot('question_id', $question->getKey())->updateExistingPivot($question->getKey(), [
                    'answer_id' => null,
                    'status_solved_question' => 'unanswered',
                    /*'have_been_show_test' => 'no'*/
                ]);
            }
        } catch (\Exception $e) {
            //DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function grade_a_test($request, $test)
    {
        try {

            $test = Test::query()->findOrFail($test->getKey());
            $user = auth()?->user();

            $total_questions_test = $test->questions->count();
            $totalQuestionsCorrect = $test->questions()->wherePivot('status_solved_question', 'correct')->get()->count();
            $totalQuestionsWrong = $test->questions()->wherePivot('status_solved_question', 'wrong')->get()->count();
            $totalQuestionsUnanswered = $test->questions()->wherePivot('status_solved_question', 'unanswered')->get()->count();

            $test->total_questions_correct = $totalQuestionsCorrect;
            $test->total_questions_wrong = $totalQuestionsWrong;
            $test->total_questions_unanswered = $totalQuestionsUnanswered;

            // Calificar test

            $result_final_test = (
                ($totalQuestionsCorrect - ($totalQuestionsWrong / 3))
                / ($total_questions_test / 10)
            );


            $test->test_result = ((int) $result_final_test) <= 0 ? '0' : number_format($result_final_test, 2);
            $test->is_solved_test = 'yes';
            $test->finished_at = Carbon::now();

            $test->save();

            $test->refresh();

            foreach ($test->questions()->wherePivot('status_solved_question', 'correct')->get() as $question) {
                $test->questions()->wherePivot('question_id', $question->getKey())->updateExistingPivot($question->getKey(), [
                    'have_been_show_test' => 'yes'
                ]);
            }

            DB::select(
                "call update_used_questions_procedure(?)",
                [$test->id]
            );



            return $test;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    public function fetch_test_completed($test)
    {
        $testQuery = Auth::user()->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'yes')->where('id', '=', $test->getKey())->first();

        if (!$testQuery) {
            abort(404);
        }

        return QueryParametersQuestionsForResolveTest::getQuestionsEloquentSortByIndexByTest($testQuery);
    }
}