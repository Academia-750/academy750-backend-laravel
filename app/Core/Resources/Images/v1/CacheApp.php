<?php
namespace App\Core\Resources\Images\v1;

use App\Models\Image;
use App\Core\Resources\Images\v1\Interfaces\ImagesInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Images\v1\DBApp;
class CacheApp implements ImagesInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'image.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('image')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('image')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $image ){

        return Cache::store('redis')->tags('image')->rememberForever("image.find.".$image->getRouteKey(), function () use ( $image ) {
            return $this->dbApp->read( $image );
        });
    }

    public function update( $request, $image ){

        Cache::store('redis')->tags('image')->flush();

        return $this->dbApp->update( $request, $image );
    }

    public function delete( $image ): void{

        Cache::store('redis')->tags('image')->flush();
        $this->dbApp->delete( $image );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('image')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('image')->flush();
        $this->dbApp->import_records( $request );
    }

}
