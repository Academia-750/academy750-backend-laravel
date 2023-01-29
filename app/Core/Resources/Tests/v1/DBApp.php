<?php
namespace App\Core\Resources\Tests\v1;

use App\Core\Resources\Tests\Services\QuestionsTestService;
use App\Core\Resources\Tests\Services\TestsQuestionsService;
use App\Core\Resources\Tests\Services\TestsService;
use App\Models\Answer;
use App\Models\Opposition;
use App\Models\Question;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Models\TestType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DBApp implements TestsInterface
{
    protected Test $model;

    public function __construct(Test $test ){
        $this->model = $test;
    }

    public function get_tests_unresolved(){
        return Auth::user()?->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'no')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_cards_memory()
    {
        return Auth::user()?->tests()->where('test_type', '=', 'card_memory')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function fetch_unresolved_test( $test ){

        $testQuery = Auth::user()->tests()->where('test_type', '=', 'test')->where('id', '=', $test->getRouteKey())->first();

        if (!$testQuery) {
            abort(404);
        }

        return TestsQuestionsService::getQuestionsEloquentSortByIndexByTest($testQuery);
    }

    public function fetch_card_memory( $test ){

        $testQuery = Auth::user()->tests()->where('test_type', '=', 'card_memory')->firstWhere('id', '=', $test->getRouteKey());

        if (!$testQuery) {
            abort(404);
        }

        return Question::query()->whereIn('id', $testQuery->questions()->pluck('questions.id')->toArray())->jsonPaginate();
    }

    public function create_a_quiz( $request )
    {
        try {

            DB::beginTransaction();
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
                $questionnaire,
                $request->get('topics_id'),
                $request->get('opposition_id')
            );
            DB::commit();

            return $questionnaire;
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    /**
     * Resuelve una pregunta de un Test abierto
     * Simplemente envía el Test, la pregunta y la respuesta que se ha escojido
     *
     * @param $request
     * @return void
     */
    public function resolve_a_question_of_test($request)
    {
        try {

            DB::beginTransaction();
            $test = Test::findOrFail($request->get('test_id'));

            $question = $test->questions()->findOrFail($request->get('question_id'));

            $answer = Answer::query()->find($request->get('answer_id'));

            if ($request->get('answer_id')) {

                $stateQuestionAnswered = $answer->is_correct_answer === 'yes' ? 'correct' : 'wrong';

                $test->questions()->orderBy('index', 'ASC')->wherePivot('question_id', $question->id)->updateExistingPivot($question->getRouteKey(), [
                   'answer_id' => $answer->getRouteKey(),
                   'status_solved_question' => $stateQuestionAnswered,
                    /*'have_been_show_test' => $stateQuestionAnswered === 'wrong' ? 'no' : 'yes'*/
                ]);

            } else {
                $test->questions()->orderBy('index', 'ASC')->wherePivot('question_id', $question->id)->updateExistingPivot($question->getRouteKey(), [
                    'answer_id' => null,
                    'status_solved_question' => 'unanswered',
                    /*'have_been_show_test' => 'no'*/
                ]);
            }



            DB::commit();


        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function grade_a_test($request, $test)
    {
        try {

            DB::beginTransaction();
            $test = Test::query()->findOrFail($test->getRouteKey());
            //\Log::debug($test);

            $total_questions_test = $test->questions->count();
            //\Log::debug($total_questions_test);
            $totalQuestionsCorrect = $test->questions()->orderBy('index', 'ASC')->wherePivot('status_solved_question', 'correct')->get()->count();
            //\Log::debug($totalQuestionsCorrect);
            $totalQuestionsWrong = $test->questions()->orderBy('index', 'ASC')->wherePivot('status_solved_question', 'wrong')->get()->count();
            //\Log::debug($totalQuestionsWrong);
            $totalQuestionsUnanswered = $test->questions()->orderBy('index', 'ASC')->wherePivot('status_solved_question', 'unanswered')->get()->count();
            //\Log::debug($totalQuestionsUnanswered);

            $test->total_questions_correct = $totalQuestionsCorrect;
            $test->total_questions_wrong = $totalQuestionsWrong;
            $test->total_questions_unanswered = $totalQuestionsUnanswered;

            // Calificar test

            /*$result_final_test = (
                $totalQuestionsCorrect - ($totalQuestionsWrong / 3) / $total_questions_test / 10
            );*/
            $result_final_test = (
                ( $totalQuestionsCorrect - ($totalQuestionsWrong / 3) )
                / ($total_questions_test / 10)
            );

            //\Log::debug($result_final_test);

            $test->test_result = ( (int) $result_final_test ) <= 0 ? '0' : number_format($result_final_test, 2);
            $test->is_solved_test = 'yes';
            $test->finished_at = Carbon::now();

            $test->save();

            $test->refresh();

            /*foreach ($test->questions()->wherePivot('status_solved_question', 'correct')->get() as $question) {
                $question->updateExistingPivot($question->getRouteKey(), [
                    'have_been_show_test' => 'yes'
                ]);
            }*/

            DB::commit();

            return $test;
        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(),$e->getMessage());
        }
    }

    public function fetch_test_completed($test)
    {
        $testQuery = Auth::user()->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'yes')->where('id', '=', $test->getRouteKey())->first();

        if (!$testQuery) {
            abort(404);
        }

        return TestsQuestionsService::getQuestionsEloquentSortByIndexByTest($testQuery);
    }
}
