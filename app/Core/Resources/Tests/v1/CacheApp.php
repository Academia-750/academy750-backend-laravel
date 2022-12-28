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

    public function get_tests_unresolved(){

        /*$nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'test.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('test')->rememberForever($nameCache, function () {
            return $this->dbApp->get_tests_unresolved();
        });*/

        return $this->dbApp->get_tests_unresolved();

    }

    public function fetch_unresolved_test( $test ){

        /*return Cache::store('redis')->tags('test')->rememberForever("test.find.".$test->getRouteKey(), function () use ( $test ) {
            return $this->dbApp->fetch_unresolved_test( $test );
        });*/

        return $this->dbApp->fetch_unresolved_test( $test );
    }
    public function fetch_card_memory( $test ){

        /*return Cache::store('redis')->tags('test')->rememberForever("test.find.".$test->getRouteKey(), function () use ( $test ) {
            return $this->dbApp->fetch_unresolved_test( $test );
        });*/

        return $this->dbApp->fetch_card_memory( $test );
    }

    public function create_a_quiz ( $request ) {
        //Cache::store('redis')->tags('test')->flush();

        return $this->dbApp->create_a_quiz( $request );
    }

    public function get_cards_memory()
    {
        return $this->dbApp->get_cards_memory();
    }

    public function resolve_a_question_of_test($request)
    {
        return $this->dbApp->resolve_a_question_of_test($request);
    }

    public function grade_a_test($request, $test)
    {
        return $this->dbApp->grade_a_test($request, $test);

    }

    public function fetch_test_completed($test)
    {
        return $this->dbApp->fetch_test_completed($test);
    }
}
