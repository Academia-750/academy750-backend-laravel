<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Questions\v1\CacheApp;
class EventApp implements QuestionsInterface
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
        /* broadcast(new CreateQuestionEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $question ){
        return $this->cacheApp->read( $question );
    }

    public function update( $request, $question ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $question );
        /* broadcast(new UpdateQuestionEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $question ): void{
        /* broadcast(new DeleteQuestionEvent($question)); */
        $this->cacheApp->delete( $question );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = Question::whereIn('id', $request->get('questions'));

        broadcast(
            new ActionForMassiveSelectionQuestionEvent( $request->get('action'), $records )
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
