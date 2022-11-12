<?php
namespace App\Core\Resources\TestTypes\v1;

use App\Models\TestType;
use App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\TestTypes\v1\DBApp;
class CacheApp implements TestTypesInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'test_type.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('test_type')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('test_type')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $test_type ){

        return Cache::store('redis')->tags('test_type')->rememberForever("test_type.find.".$test_type->getRouteKey(), function () use ( $test_type ) {
            return $this->dbApp->read( $test_type );
        });
    }

    public function update( $request, $test_type ){

        Cache::store('redis')->tags('test_type')->flush();

        return $this->dbApp->update( $request, $test_type );
    }

    public function delete( $test_type ): void{

        Cache::store('redis')->tags('test_type')->flush();
        $this->dbApp->delete( $test_type );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('test_type')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('test_type')->flush();
        $this->dbApp->import_records( $request );
    }

}
