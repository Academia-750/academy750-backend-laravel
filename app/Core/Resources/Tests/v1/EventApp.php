<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Tests\v1\CacheApp;
class EventApp implements TestsInterface
{
    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function get_tests_unresolved(){
        return $this->cacheApp->get_tests_unresolved();
    }

    public function fetch_unresolved_test( $test ){
        return $this->cacheApp->fetch_unresolved_test( $test );
    }

    public function create_a_quiz( $request ){
        return $this->cacheApp->create_a_quiz( $request );
    }


}
