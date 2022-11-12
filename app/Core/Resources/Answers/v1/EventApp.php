<?php
namespace App\Core\Resources\Answers\v1;

use App\Models\Answer;
use App\Core\Resources\Answers\v1\Interfaces\AnswersInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Answers\v1\CacheApp;
class EventApp implements AnswersInterface
{
    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function create( $request ){
        $itemCreatedInstance = $this->cacheApp->create( $request );
        /* broadcast(new CreateAnswerEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $answer ){
        return $this->cacheApp->read( $answer );
    }

    public function update( $request, $answer ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $answer );
        /* broadcast(new UpdateAnswerEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $answer ): void{
        /* broadcast(new DeleteAnswerEvent($answer)); */
        $this->cacheApp->delete( $answer );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = Answer::whereIn('id', $request->get('answers'));

        broadcast(
            new ActionForMassiveSelectionAnswerEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ): void{
        $this->cacheApp->import_records( $request );
    }

}
