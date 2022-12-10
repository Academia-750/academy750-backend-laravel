<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Questions\v1\DBApp;
class CacheApp implements QuestionsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'question.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('question')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('question')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $question ){

        return Cache::store('redis')->tags('question')->rememberForever("question.find.".$question->getRouteKey(), function () use ( $question ) {
            return $this->dbApp->read( $question );
        });
    }

    public function update( $request, $question ){

        Cache::store('redis')->tags('question')->flush();

        return $this->dbApp->update( $request, $question );
    }

    public function delete( $question ): void{

        Cache::store('redis')->tags('question')->flush();
        $this->dbApp->delete( $question );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('question')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('question')->flush();
        $this->dbApp->import_records( $request );
    }
    
    public function generate(){
        return $this->dbApp->generate();
    }

}
