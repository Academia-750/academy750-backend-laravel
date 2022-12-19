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

    public function index(): QuestionnaireCollection
    {
        Gate::authorize('index', Test::class );
        return $this->schemaJson->index();
    }

    public function read( $test ): QuestionnaireResource
    {
        Gate::authorize('read', $test );

        return $this->schemaJson->read( $test );
    }

    public function generate( $request )
    {
        Gate::authorize('generate', [Test::class, $request] );
        return $this->schemaJson->generate( $request );
    }


}
