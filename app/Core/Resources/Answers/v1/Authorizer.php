<?php
namespace App\Core\Resources\Answers\v1;

use App\Models\Answer;
use App\Core\Resources\Answers\v1\Interfaces\AnswersInterface;
use App\Http\Resources\Api\Answer\v1\AnswerCollection;
use App\Http\Resources\Api\Answer\v1\AnswerResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Answers\v1\SchemaJson;
class Authorizer implements AnswersInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): AnswerCollection
    {
        Gate::authorize('index', Answer::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Answer::class );
        return $this->schemaJson->create($request);
    }

    public function read( $answer ): AnswerResource
    {
        Gate::authorize('read', $answer );
        return $this->schemaJson->read( $answer );
    }

    public function update( $request, $answer ): AnswerResource
    {
        Gate::authorize('update', $answer );
        return $this->schemaJson->update( $request, $answer );
    }

    public function delete( $answer ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $answer );
        return $this->schemaJson->delete( $answer );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Answer::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Answer::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Answer::class );
        return $this->schemaJson->import_records( $request );
    }

}
