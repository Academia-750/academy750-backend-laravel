<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\TopicGroups\v1\DBApp;
class CacheApp implements TopicGroupsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'topic_group.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('topic_group')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('topic_group')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $topic_group ){

        return Cache::store('redis')->tags('topic_group')->rememberForever("topic_group.find.".$topic_group->getRouteKey(), function () use ( $topic_group ) {
            return $this->dbApp->read( $topic_group );
        });
    }

    public function update( $request, $topic_group ){

        Cache::store('redis')->tags('topic_group')->flush();

        return $this->dbApp->update( $request, $topic_group );
    }

    public function delete( $topic_group ): void{

        Cache::store('redis')->tags('topic_group')->flush();
        $this->dbApp->delete( $topic_group );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('topic_group')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('topic_group')->flush();
        $this->dbApp->import_records( $request );
    }

}
