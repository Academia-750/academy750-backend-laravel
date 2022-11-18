<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CacheApp implements OppositionsInterface
{
    protected $dbApp;

    public function __construct(\App\Core\Resources\Oppositions\v1\DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'opposition.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('opposition')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('opposition')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $opposition ){

        return Cache::store('redis')->tags('opposition')->rememberForever("opposition.find.".$opposition->getRouteKey(), function () use ( $opposition ) {
            return $this->dbApp->read( $opposition );
        });
    }

    public function update( $request, $opposition ){

        Cache::store('redis')->tags('opposition')->flush();

        return $this->dbApp->update( $request, $opposition );
    }

    public function delete( $opposition ){

        Cache::store('redis')->tags('opposition')->flush();

        return $this->dbApp->delete( $opposition );
    }

    public function mass_selection_for_action( $request ): array{

        Cache::store('redis')->tags('opposition')->flush();

        return $this->dbApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->dbApp->export_records( $request );
    }

    public function import_records( $request ){
        Cache::store('redis')->tags('opposition')->flush();

        return $this->dbApp->import_records( $request );
    }

    public function get_relationship_syllabus($opposition)
    {
        return Cache::store('redis')->tags('topics')->rememberForever("opposition.relationship.topics.".$opposition->getRouteKey(), function () use ( $opposition ) {
            return $this->dbApp->get_relationship_syllabus( $opposition );
        });
    }
}
