<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupCollection;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupResource;

class SchemaJson implements TopicGroupsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): TopicGroupCollection
    {
        return TopicGroupCollection::make(
            $this->eventApp->index()
        );
    }

    public function read( $topic_group ): TopicGroupResource
    {
        return TopicGroupResource::make(
            $this->eventApp->read( $topic_group )
        );
    }
}
