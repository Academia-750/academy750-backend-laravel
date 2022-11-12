<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupCollection;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\TopicGroups\v1\SchemaJson;
class Authorizer implements TopicGroupsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): TopicGroupCollection
    {
        Gate::authorize('index', TopicGroup::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', TopicGroup::class );
        return $this->schemaJson->create($request);
    }

    public function read( $topic_group ): TopicGroupResource
    {
        Gate::authorize('read', $topic_group );
        return $this->schemaJson->read( $topic_group );
    }

    public function update( $request, $topic_group ): TopicGroupResource
    {
        Gate::authorize('update', $topic_group );
        return $this->schemaJson->update( $request, $topic_group );
    }

    public function delete( $topic_group ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $topic_group );
        return $this->schemaJson->delete( $topic_group );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', TopicGroup::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', TopicGroup::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', TopicGroup::class );
        return $this->schemaJson->import_records( $request );
    }

}
