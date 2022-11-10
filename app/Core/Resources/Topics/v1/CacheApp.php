<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Topics\v1\DBApp;
class CacheApp implements TopicsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'topic.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('topic')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('topic')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $topic ){

        return Cache::store('redis')->tags('topic')->rememberForever("topic.find.".$topic->getRouteKey(), function () use ( $topic ) {
            return $this->dbApp->read( $topic );
        });
    }

    public function update( $request, $topic ){

        Cache::store('redis')->tags('topic')->flush();

        return $this->dbApp->update( $request, $topic );
    }

    public function delete( $topic ): void{

        Cache::store('redis')->tags('topic')->flush();
        $this->dbApp->delete( $topic );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('topic')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('topic')->flush();
        $this->dbApp->import_records( $request );
    }

}
