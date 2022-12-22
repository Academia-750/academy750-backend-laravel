<?php
namespace App\Core\Resources\Tests\v1;

use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireResource;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
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

    public function fetch_unresolved_test( $test ): QuestionnaireResource
    {
        Gate::authorize('fetch_unresolved_test', $test );

        return $this->schemaJson->fetch_unresolved_test( $test );
    }

    public function create_a_quiz( $request )
    {
        Gate::authorize('create_a_quiz', [Test::class, $request] );
        return $this->schemaJson->create_a_quiz( $request );
    }


}
