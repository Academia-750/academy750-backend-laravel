<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupCollection;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupResource;
use Illuminate\Support\Facades\Gate;
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

    public function read( $topic_group ): TopicGroupResource
    {
        Gate::authorize('read', $topic_group );
        return $this->schemaJson->read( $topic_group );
    }

}
