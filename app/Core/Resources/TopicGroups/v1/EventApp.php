<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
class EventApp implements TopicGroupsInterface
{
    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function read( $topic_group ){
        return $this->cacheApp->read( $topic_group );
    }
}
