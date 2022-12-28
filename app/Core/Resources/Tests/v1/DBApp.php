<?php
namespace App\Core\Resources\Tests\v1;

use App\Core\Resources\Tests\Services\QuestionsTestService;
use App\Core\Resources\Tests\Services\TestsService;
use App\Models\Answer;
use App\Models\Opposition;
use App\Models\Question;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Models\TestType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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

    public function fetch_unresolved_test( $test ){

        $testQuery = Auth::user()->tests()->where('test_type', '=', 'test')->where('id', '=', $test->getRouteKey())->first();

        if (!$testQuery) {
            abort(404);
        }

        return Question::query()->whereIn('id', $testQuery->questions()->pluck('questions.id')->toArray())->with(['answers_by_test'])->jsonPaginate();
    }

    public function fetch_card_memory( $test ){

        $testQuery = Auth::user()->tests()->firstWhere('id', '=', $test->getRouteKey());

        if (!$testQuery) {
            abort(404);
        }

        return Question::query()->whereIn('id', $testQuery->questions()->pluck('questions.id')->toArray())->with(['answers', 'image'])->jsonPaginate();
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
                $questionnaire
            );
            DB::commit();

            return $questionnaire;
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function resolve_a_question_of_test($request)
    {
        try {

            DB::beginTransaction();
            $test = Test::findOrFail($request->get('test_id'));

            $question = $test->questions()->findOrFail($request->get('question_id'));

            $answer = Answer::query()->find($request->get('answer_id'));
            \Log::debug($answer);

            if ($request->get('answer_id')) {

                \Log::debug($answer->is_correct_answer === 'yes' ? 'correct' : 'wrong');

                $test->questions()->wherePivot('question_id', $question->id)->updateExistingPivot($question->getRouteKey(), [
                   'answer_id' => $answer->getRouteKey(),
                   'status_solved_question' => $answer->is_correct_answer === 'yes' ? 'correct' : 'wrong'
                ]);

            } else {
                \Log::debug('Se esta guardando el resultado de una respuesta incorrecta');
                $test->questions()->wherePivot('question_id', $question->id)->updateExistingPivot($question->getRouteKey(), [
                    'answer_id' => null,
                    'status_solved_question' => 'unanswered'
                ]);
            }



            DB::commit();


        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }
}
