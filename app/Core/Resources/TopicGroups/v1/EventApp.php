<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\TopicGroups\v1\CacheApp;
class EventApp implements TopicGroupsInterface
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
        /* broadcast(new CreateTopicGroupEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $topic_group ){
        return $this->cacheApp->read( $topic_group );
    }

    public function update( $request, $topic_group ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $topic_group );
        /* broadcast(new UpdateTopicGroupEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $topic_group ): void{
        /* broadcast(new DeleteTopicGroupEvent($topic_group)); */
        $this->cacheApp->delete( $topic_group );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = TopicGroup::whereIn('id', $request->get('topic_groups'));

        broadcast(
            new ActionForMassiveSelectionTopicGroupEvent( $request->get('action'), $records )
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
