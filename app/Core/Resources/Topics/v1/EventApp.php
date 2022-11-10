<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Topics\v1\CacheApp;
class EventApp implements TopicsInterface
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
        /* broadcast(new CreateTopicEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $topic ){
        return $this->cacheApp->read( $topic );
    }

    public function update( $request, $topic ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $topic );
        /* broadcast(new UpdateTopicEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $topic ): void{
        /* broadcast(new DeleteTopicEvent($topic)); */
        $this->cacheApp->delete( $topic );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = Topic::whereIn('id', $request->get('topics'));

        broadcast(
            new ActionForMassiveSelectionTopicEvent( $request->get('action'), $records )
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
