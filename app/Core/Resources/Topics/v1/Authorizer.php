<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use App\Http\Resources\Api\Topic\v1\TopicResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Topics\v1\SchemaJson;
class Authorizer implements TopicsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): TopicCollection
    {
        Gate::authorize('index', Topic::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Topic::class );
        return $this->schemaJson->create($request);
    }

    public function read( $topic ): TopicResource
    {
        Gate::authorize('read', $topic );
        return $this->schemaJson->read( $topic );
    }

    public function update( $request, $topic ): TopicResource
    {
        Gate::authorize('update', $topic );
        return $this->schemaJson->update( $request, $topic );
    }

    public function delete( $topic ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $topic );
        return $this->schemaJson->delete( $topic );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Topic::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Topic::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Topic::class );
        return $this->schemaJson->import_records( $request );
    }

    public function get_relationship_subtopics($topic)
    {
        Gate::authorize('get_relationship_subtopics', $topic );
        return $this->schemaJson->get_relationship_subtopics($topic);
    }
}
