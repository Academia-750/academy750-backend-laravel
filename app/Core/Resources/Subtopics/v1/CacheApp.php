<?php
namespace App\Core\Resources\Subtopics\v1;

use App\Models\Subtopic;
use App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Subtopics\v1\DBApp;
class CacheApp implements SubtopicsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'subtopic.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('subtopic')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('subtopic')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $subtopic ){

        return Cache::store('redis')->tags('subtopic')->rememberForever("subtopic.find.".$subtopic->getRouteKey(), function () use ( $subtopic ) {
            return $this->dbApp->read( $subtopic );
        });
    }

    public function update( $request, $subtopic ){

        Cache::store('redis')->tags('subtopic')->flush();

        return $this->dbApp->update( $request, $subtopic );
    }

    public function delete( $subtopic ): void{

        Cache::store('redis')->tags('subtopic')->flush();
        $this->dbApp->delete( $subtopic );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('subtopic')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('subtopic')->flush();
        $this->dbApp->import_records( $request );
    }

}
