<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CacheApp implements UsersInterface
{
    protected DBApp $dbApp;

    public function __construct(\App\Core\Resources\Users\v1\DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    /**
     * @throws \JsonException
     */
    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'user.get.all' : $nameCache = json_encode(request()->query(), JSON_THROW_ON_ERROR);

        return Cache::store('redis')->tags('user')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $user ){

        return Cache::store('redis')->tags('user')->rememberForever("user.find.".$user->getRouteKey(), function () use ( $user ) {
            return $this->dbApp->read( $user );
        });
    }

    public function update( $request, $user ){

        Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->update( $request, $user );
    }

    public function delete( $user ): void{

        Cache::store('redis')->tags('user')->flush();
        $this->dbApp->delete( $user );
    }

    public function mass_selection_for_action( $request ): string{

        Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->dbApp->export_records( $request );
    }

    public function import_records( $request ){
        Cache::store('redis')->tags('user')->flush();

        return $this->dbApp->import_records( $request );
    }

}
