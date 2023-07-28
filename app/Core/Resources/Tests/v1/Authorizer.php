<?php
namespace App\Core\Resources\Tests\v1;

use App\Http\Resources\Api\Question\v1\QuestionByTestCollection;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireResource;
use App\Models\Opposition;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        $opposition_id = $request->get('opposition_id');

        $opposition = Opposition::query()
            ->where('id', $opposition_id)
            ->orWhere('uuid', $opposition_id)
            ->first();

        if (!$opposition) {
            throw new ModelNotFoundException("La oposicion con Identificador {$opposition_id} no fue encontrado.");
        }

        $topicsBelongsToOpposition = true;

        $topics_uuid_by_opposition = $opposition->topics()->pluck('topics.uuid')->toArray();

        foreach ($request->get('topics_id') as $topic_uuid) {
            if ( !in_array($topic_uuid, $topics_uuid_by_opposition, true) ) {
                $topicsBelongsToOpposition = false;
            }
        }

        if (!(Auth::user()->can('create-tests-for-resolve') && (bool) $topicsBelongsToOpposition)) {
            abort(403, "Posiblemente algunos o todos los temas seleccionados no pertenecen a la Oposición Seleccionada");
        }

        return $this->schemaJson->create_a_quiz( $request );
    }

    public function get_cards_memory()
    {
        Gate::authorize('get_cards_memory', Test::class );
        return $this->schemaJson->get_cards_memory();
    }

    /**
     * @throws \Throwable
     */
    public function resolve_a_question_of_test($request)
    {
        $test_id = $request->get('test_id');

        $test = Test::query()
            ->where('id', $test_id)
            ->where('uuid', $test_id)
            ->first();

        abort_if(!$test, new ModelNotFoundException("El Test o Cuestionario con Identificador {$test_id} no fue encontrado."));

        $question_id = $request->get('question_id');

        $questionQuery = $test->questions()
            ->where('id', $question_id)
            ->orWhere('uuid', $question_id)
            ->first();

        throw_if(!$questionQuery, new ModelNotFoundException("La pregunta con Identificador {$question_id} no fue encontrado."));

        if ($request->get('answer_id')) {
            $answer_id = $request->get('answer_id');

            $answerQuery = $questionQuery->answers()
                ->where('id', $answer_id)
                ->orWhere('uuid', $answer_id)
                ->first();

            throw_if(!$answerQuery, new ModelNotFoundException("La alternativa con Identificador {$answer_id} no fue encontrado."));
        }

        return $this->schemaJson->resolve_a_question_of_test($request);
    }

    public function grade_a_test($request, $test)
    {
        // \Log::debug($test);
        if (!Auth::user()->hasRole('student') || !Auth::user()->tests()->findOrFail($test->getKey())) {
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
