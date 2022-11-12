<?php
namespace App\Core\Resources\Answers\v1;

use App\Models\Answer;
use App\Core\Resources\Answers\v1\Interfaces\AnswersInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Answers\v1\DBApp;
class CacheApp implements AnswersInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'answer.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('answer')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('answer')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $answer ){

        return Cache::store('redis')->tags('answer')->rememberForever("answer.find.".$answer->getRouteKey(), function () use ( $answer ) {
            return $this->dbApp->read( $answer );
        });
    }

    public function update( $request, $answer ){

        Cache::store('redis')->tags('answer')->flush();

        return $this->dbApp->update( $request, $answer );
    }

    public function delete( $answer ): void{

        Cache::store('redis')->tags('answer')->flush();
        $this->dbApp->delete( $answer );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('answer')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('answer')->flush();
        $this->dbApp->import_records( $request );
    }

}
