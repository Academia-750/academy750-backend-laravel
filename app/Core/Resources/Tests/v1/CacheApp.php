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

    public function create( $request ){

        Cache::store('redis')->tags('test')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $test ){

        return Cache::store('redis')->tags('test')->rememberForever("test.find.".$test->getRouteKey(), function () use ( $test ) {
            return $this->dbApp->read( $test );
        });
    }

    public function update( $request, $test ){

        Cache::store('redis')->tags('test')->flush();

        return $this->dbApp->update( $request, $test );
    }

    public function delete( $test ): void{

        Cache::store('redis')->tags('test')->flush();
        $this->dbApp->delete( $test );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('test')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('test')->flush();
        $this->dbApp->import_records( $request );
    }

}
