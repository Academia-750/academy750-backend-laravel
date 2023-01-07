<?php
namespace App\Core\Resources\Tests\v1;

use App\Http\Resources\Api\Question\v1\QuestionByTestCollection;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireResource;
use App\Models\Question;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;

use App\Core\Resources\Tests\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TestsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function get_tests_unresolved(): QuestionnaireCollection
    {
        return QuestionnaireCollection::make(
            $this->eventApp->get_tests_unresolved()
        );
    }

    public function fetch_unresolved_test( $test ): QuestionByTestCollection
    {
        $questions = collect([]);



        $questionsQuery = Question::query()->whereIn('id', $test->questions()->orderBy('index', 'ASC')->pluck('questions.id')->toArray())->get();

        foreach ($questionsQuery as $question) {

            $questionPivotTest = $test->questions()->find($question->getRouteKey());

            $questions->push([
                "index" => $questionPivotTest?->pivot?->index,
                //"question" => $test->questions()->find($question->getRouteKey()),
                'question_id' => $question->id,
                'answer_id' => $questionPivotTest?->pivot?->answer_id,
            ]);
        }

        $countQuestionsAnswered = $test->questions()->where('status_solved_question', '<>', 'unanswered')->count();

        return QuestionByTestCollection::make(
            $this->eventApp->fetch_unresolved_test( $test )
        )->additional([
            'meta' => [
                'test' => QuestionnaireResource::make($test),
                'questions_data' => $questions,
                'number_of_questions_answered_of_test' => $countQuestionsAnswered,
                'total_questions_of_this_test' => $test->questions->count()
            ]
        ]);
    }

    public function fetch_card_memory( $test ): QuestionCollection
    {

        return QuestionCollection::make(
            $this->eventApp->fetch_card_memory( $test )
        )->additional([
            'meta' => [
                'test' => QuestionnaireResource::make($test)

            ]
        ]);
    }

    public function create_a_quiz( $request )
    {
        return QuestionnaireResource::make(
            $this->eventApp->create_a_quiz( $request )
        );
    }


    public function get_cards_memory()
    {
        return QuestionnaireCollection::make(
            $this->eventApp->get_cards_memory()
        );
    }

    public function resolve_a_question_of_test($request)
    {
        $this->eventApp->resolve_a_question_of_test($request);

        $test = Test::query()->find($request->get('test_id'));

        $countQuestionsAnswered = $test->questions()->where('status_solved_question', '<>', 'unanswered')->count();

        return response()->json([
            'status' => 'successfully',
            'number_of_questions_answered_of_test' => $countQuestionsAnswered,
            'total_questions_of_this_test' => $test->questions->count()
        ]);
    }

    public function grade_a_test($request, $test)
    {
        return QuestionnaireResource::make(
            $this->eventApp->grade_a_test($request, $test)
        );
    }

    public function fetch_test_completed($test)
    {
        $questions = collect([]);

        $questionsQuery = Question::query()->whereIn(
            'id', $test->questions()->orderBy('index', 'ASC')->pluck('questions.id')->toArray()
        )->get();

        foreach ($questionsQuery as $question) {

            $questionPivotTest = $test->questions()->find($question->getRouteKey());

            $questions->push([
                "index" => $questionPivotTest?->pivot?->index,
                "status_question" => $questionPivotTest?->pivot?->status_solved_question,
                "question" => $question->question,
                'question_id' => $question->id,
                'answer_id' => $questionPivotTest?->pivot?->answer_id,
            ]);
        }

        return QuestionCollection::make(
            $this->eventApp->fetch_test_completed( $test )
        )->additional([
            'meta' => [
                'test' => QuestionnaireResource::make($test),
                'questions_data' => $questions
            ]
        ]);
    }
}
