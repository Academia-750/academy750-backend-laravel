<?php
namespace App\Core\Resources\Tests\v1;

use App\Http\Resources\Api\Question\v1\QuestionByTestCollection;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireResource;
use App\Models\Opposition;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Tests\v1\SchemaJson;
class Authorizer implements TestsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function get_tests_unresolved(): QuestionnaireCollection
    {
        Gate::authorize('get_tests_unresolved', Test::class );
        return $this->schemaJson->get_tests_unresolved();
    }

    public function fetch_unresolved_test( $test ): QuestionByTestCollection
    {
        Gate::authorize('fetch_unresolved_test', $test );

        return $this->schemaJson->fetch_unresolved_test( $test );
    }

    public function fetch_card_memory( $test ): QuestionCollection
    {
        Gate::authorize('fetch_card_memory', $test );

        return $this->schemaJson->fetch_card_memory( $test );
    }

    public function create_a_quiz( $request )
    {
        //Gate::authorize('create_a_quiz', [Auth::user(), Test::class, Opposition::findOrFail($request->get('opposition_id')), $request] );
        $opposition = Opposition::findOrFail($request->get('opposition_id'));

        $topicsBelongsToOpposition = true;

        $topics_id_by_opposition = $opposition->topics()->pluck('topics.id')->toArray();

        foreach ($request->get('topics_id') as $topic_id) {
            if ( !in_array($topic_id, $topics_id_by_opposition, true) ) {
                $topicsBelongsToOpposition = false;
            }
        }

        if (!(Auth::user()->can('create-tests-for-resolve') && (bool) $topicsBelongsToOpposition)) {
            abort(403);
        }

        return $this->schemaJson->create_a_quiz( $request );
    }

    public function get_cards_memory()
    {
        Gate::authorize('get_cards_memory', Test::class );
        return $this->schemaJson->get_cards_memory();
    }

    public function resolve_a_question_of_test($request)
    {
        $test = Test::findOrFail($request->get('test_id'));

        $question = $test->questions()->find($request->get('question_id'));

        if (!$question) {
            abort(403);
        }

        if ($request->get('answer_id')) {
            $answer = $question->answers()->find($request->get('answer_id'));

            if (!$answer) {
                abort(403);
            }
        }

        return $this->schemaJson->resolve_a_question_of_test($request);
    }

    public function grade_a_test($request, $test)
    {
        \Log::debug($test);
        if (!Auth::user()->hasRole('student') || !Auth::user()->tests()->find($test->getRouteKey())) {
            abort(403);
        }

        return $this->schemaJson->grade_a_test($request, $test);
    }

    public function fetch_test_completed($test)
    {
        Gate::authorize('fetch_test_completed', $test );

        return $this->schemaJson->fetch_test_completed($test);
    }
}
