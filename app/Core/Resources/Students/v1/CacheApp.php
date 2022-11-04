<?php
namespace App\Core\Resources\Students\v1;

use App\Models\Student;
use App\Core\Resources\Students\v1\Interfaces\StudentsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CacheApp implements StudentsInterface
{
    protected DBApp $dbApp;

    public function __construct(\App\Core\Resources\Students\v1\DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    /**
     * @throws \JsonException
     */
    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'student.get.all' : $nameCache = json_encode(request()->query(), JSON_THROW_ON_ERROR);

        return Cache::store('redis')->tags('student')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('student')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $student ){

        return Cache::store('redis')->tags('student')->rememberForever("student.find.".$student->getRouteKey(), function () use ( $student ) {
            return $this->dbApp->read( $student );
        });
    }

    public function update( $request, $student ){

        Cache::store('redis')->tags('student')->flush();

        return $this->dbApp->update( $request, $student );
    }

    public function delete( $student ){

        Cache::store('redis')->tags('student')->flush();

        return $this->dbApp->delete( $student );
    }

    public function mass_selection_for_action( $request ): string{

        Cache::store('redis')->tags('student')->flush();

        return $this->dbApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->dbApp->export_records( $request );
    }

    public function import_records( $request ){
        Cache::store('redis')->tags('student')->flush();

        return $this->dbApp->import_records( $request );
    }

}
