<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Question\v1\QuestionResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Questions\v1\SchemaJson;
class Authorizer implements QuestionsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): QuestionCollection
    {
        Gate::authorize('index', Question::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Question::class );
        return $this->schemaJson->create($request);
    }

    public function read( $question ): QuestionResource
    {
        Gate::authorize('read', $question );
        return $this->schemaJson->read( $question );
    }

    public function update( $request, $question ): QuestionResource
    {
        Gate::authorize('update', $question );
        return $this->schemaJson->update( $request, $question );
    }

    public function delete( $question ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $question );
        return $this->schemaJson->delete( $question );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Question::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Question::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Question::class );
        return $this->schemaJson->import_records( $request );
    }

}
