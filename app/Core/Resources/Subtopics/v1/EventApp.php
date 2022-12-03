<?php
namespace App\Core\Resources\Subtopics\v1;

use App\Models\Subtopic;
use App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Subtopics\v1\CacheApp;
class EventApp implements SubtopicsInterface
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
        /* broadcast(new CreateSubtopicEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $subtopic ){
        return $this->cacheApp->read( $subtopic );
    }

    public function update( $request, $subtopic ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $subtopic );
        /* broadcast(new UpdateSubtopicEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $subtopic ): void{
        /* broadcast(new DeleteSubtopicEvent($subtopic)); */
        $this->cacheApp->delete( $subtopic );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = Subtopic::whereIn('id', $request->get('subtopics'));

        broadcast(
            new ActionForMassiveSelectionSubtopicEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ): void{
        $this->cacheApp->import_records( $request );
    }

    public function subtopic_get_relationship_questions($subtopic)
    {
        return $this->cacheApp->subtopic_get_relationship_questions($subtopic);
    }

    public function subtopic_get_a_question($subtopic, $question)
    {
        return $this->cacheApp->subtopic_get_a_question($subtopic, $question);
    }

    public function subtopic_create_a_question($request, $subtopic)
    {
        return $this->cacheApp->subtopic_create_a_question($request, $subtopic);
    }

    public function subtopic_update_a_question($request, $subtopic, $question)
    {
        return $this->cacheApp->subtopic_update_a_question($request, $subtopic, $question);
    }

    public function subtopic_delete_a_question($subtopic, $question)
    {
        return $this->cacheApp->subtopic_delete_a_question($subtopic, $question);
    }
}
