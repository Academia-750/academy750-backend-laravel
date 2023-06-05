<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;


class DBApp implements TopicGroupsInterface
{
    protected TopicGroup $model;

    public function __construct(TopicGroup $topic_group ){
        $this->model = $topic_group;
    }

    public function index(){
        return $this->model
            ->applyFilters()
            ->applySorts()
            ->applyIncludes()
            ->jsonPaginate();
    }

    public function read( $topic_group ): \App\Models\TopicGroup{
        return $this
            ->model
            ->applyIncludes()
            ->findOrFail($topic_group->getKey());
    }
}
