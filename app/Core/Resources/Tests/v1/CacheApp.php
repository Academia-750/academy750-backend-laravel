<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Tests\v1\DBApp;
class CacheApp implements TestsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'test.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('test')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function read( $test ){

        return Cache::store('redis')->tags('test')->rememberForever("test.find.".$test->getRouteKey(), function () use ( $test ) {
            return $this->dbApp->read( $test );
        });
    }

    public function generate ( $request ) {
        Cache::store('redis')->tags('test')->flush();

        return $this->dbApp->generate( $request );
    }

}
