<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
class CacheApp implements TopicGroupsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        return $this->dbApp->index();
    }

    public function read( $topic_group ){

        return $this->dbApp->read( $topic_group );
    }
}
