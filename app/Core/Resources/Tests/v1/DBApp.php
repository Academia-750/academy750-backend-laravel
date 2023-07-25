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
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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

        $testQuery = Auth::user()
            ->tests()
            ->where('test_type', '=', 'test')
            ->where('id', '=', $test->getKey())
            ->first();

        if (!$testQuery) {
            abort(404);
        }

        return TestsQuestionsService::getQuestionsEloquentSortByIndexByTest($testQuery);
    }

    public function fetch_card_memory( $test ){

        $testQuery = Auth::user()->tests()->where('test_type', '=', 'card_memory')->firstWhere('id', '=', $test->getKey());

        if (!$testQuery) {
            abort(404);
        }

        return Question::query()->whereIn('id', $testQuery->questions()->pluck('questions.id')->toArray())->jsonPaginate();
    }

    public function create_a_quiz( $request )
    {
        try {

            // Declaracion de data o variables

            $user = Auth::user();
            $testType = $request->get('test_type');

            $topics__id = array_map(function ($__topic_uuid) {
                return Topic::query()->firstWhere('uuid', $__topic_uuid)?->getKey();
            }, $request->get('topics_id'));

            $opposition__id = Opposition::query()
                ->firstWhere('uuid', $request->get('opposition_id'))
                ?->getKey();


            // posibles aborts o disparos de errores
            abort_if(!$user, 404);

            // acciones

            $questionnaire = TestsService::createTest([
                "number_of_questions_requested" => (int) $request->get('count_questions_for_test'),
                "opposition_id" => Opposition::query()
                    ->firstWhere('uuid', $request->get('opposition_id'))
                    ?->getKey(),
                "test_type" => $testType,
                "user_id" => $user?->getKey()
            ]);

            TestsService::registerTopicsAndSubtopicsByTest(
                $questionnaire->getKey(),
                $topics__id,
                $opposition__id
            );

            QuestionsTestService::buildQuestionsTest(
                (int) $request->get('count_questions_for_test'),
                $testType,
                $user,
                $questionnaire,
                $request->get('topics_id'),
                $request->get('opposition_id')
            );
            return $questionnaire;
        } catch (\Exception $e) {
            abort(500, $e);
        }

    }

    public function resolve_a_question_of_test($request)
    {
        try {

            //DB::beginTransaction();
            $test = Test::query()
                ->where('id', $request->get('test_id'))
                ->where('uuid', $request->get('test_id'))
                ->first();

            abort_if(!$test, new ModelNotFoundException("No existe el Test con identificador {$request->get('test_id')}"));

            $question = $test->questions()
                ->where('id', $request->get('question_id'))
                ->where('uuid', $request->get('question_id'))
                ->first();
            abort_if(!$test, new ModelNotFoundException("No existe la Pregunta con identificador {$request->get('question_id')}"));

            $answer = Answer::query()
                ->where('id', $request->get('answer_id'))
                ->where('uuid', $request->get('answer_id'))
                ->first();

            abort_if(!$answer, new ModelNotFoundException("No existe la Alternativa con identificador {$request->get('answer_id')}"));

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



            //DB::commit();


        } catch (\Exception $e) {
            //DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function grade_a_test($request, $test)
    {
        try {

            //DB::beginTransaction();
            $test = Test::query()->findOrFail($test->getKey());
            $user = auth()?->user();
            //// \Log::debug($test);

            $total_questions_test = $test->questions->count();
            //// \Log::debug($total_questions_test);
            $totalQuestionsCorrect = $test->questions()->wherePivot('status_solved_question', 'correct')->get()->count();
            //// \Log::debug($totalQuestionsCorrect);
            $totalQuestionsWrong = $test->questions()->wherePivot('status_solved_question', 'wrong')->get()->count();
            //// \Log::debug($totalQuestionsWrong);
            $totalQuestionsUnanswered = $test->questions()->wherePivot('status_solved_question', 'unanswered')->get()->count();
            //// \Log::debug($totalQuestionsUnanswered);

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

            //// \Log::debug($result_final_test);

            $test->test_result = ( (int) $result_final_test ) <= 0 ? '0' : number_format($result_final_test, 2);
            $test->is_solved_test = 'yes';
            $test->finished_at = Carbon::now();

            $test->save();

            $test->refresh();

            foreach ($test->questions()->wherePivot('status_solved_question', 'correct')->get() as $question) {
                $test->questions()->wherePivot('question_id', $question->getKey())->updateExistingPivot($question->getKey(), [
                    'have_been_show_test' => 'yes'
                ]);
            }

            Log::debug("");
            Log::debug("Ejecutar procedimiento almacenado de update_used_questions para el usuario: '{$user->full_name}' con id: '{$user->id}'");
            $start_time__call_procedure_update_used_questions = microtime(true);
            DB::select(
                "call update_used_questions_procedure(?)",
                array($test->getKey())
            );

            $end_time__call_procedure_update_used_questions = (microtime(true)) - $start_time__call_procedure_update_used_questions;
            Log::debug("Ha terminado de ejecutarse el procedimiento update_used_questions para el usuario: '{$user->full_name}' con id: '{$user->id}' que ha durado: {$end_time__call_procedure_update_used_questions} segundos");

            //DB::commit();

            return $test;
        } catch (\Exception $e) {
            //DB::rollback();
            abort($e->getCode(),$e->getMessage());
        }
    }

    public function fetch_test_completed($test)
    {
        $testQuery = Auth::user()->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'yes')->where('id', '=', $test->getKey())->first();

        if (!$testQuery) {
            abort(404);
        }

        return TestsQuestionsService::getQuestionsEloquentSortByIndexByTest($testQuery);
    }
}
