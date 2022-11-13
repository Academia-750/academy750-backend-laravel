<?php
namespace App\Core\Resources\Subtopics\v1;

use App\Models\Subtopic;
use App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface;
use App\Http\Resources\Api\Subtopic\v1\SubtopicCollection;
use App\Http\Resources\Api\Subtopic\v1\SubtopicResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Subtopics\v1\SchemaJson;
class Authorizer implements SubtopicsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): SubtopicCollection
    {
        Gate::authorize('index', Subtopic::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Subtopic::class );
        return $this->schemaJson->create($request);
    }

    public function read( $subtopic ): SubtopicResource
    {
        Gate::authorize('read', $subtopic );
        return $this->schemaJson->read( $subtopic );
    }

    public function update( $request, $subtopic ): SubtopicResource
    {
        Gate::authorize('update', $subtopic );
        return $this->schemaJson->update( $request, $subtopic );
    }

    public function delete( $subtopic ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $subtopic );
        return $this->schemaJson->delete( $subtopic );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Subtopic::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Subtopic::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Subtopic::class );
        return $this->schemaJson->import_records( $request );
    }

}