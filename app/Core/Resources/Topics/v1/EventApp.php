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

    public function get_relationship_subtopics($topic)
    {
        return $this->cacheApp->get_relationship_subtopics( $topic );
    }

    public function get_relationship_oppositions($topic)
    {
        return $this->cacheApp->get_relationship_oppositions( $topic );
    }

    public function get_relationship_a_subtopic($topic, $subtopic)
    {
        return $this->cacheApp->get_relationship_a_subtopic($topic, $subtopic);
    }

    public function get_relationship_a_opposition($topic, $opposition)
    {
        return $this->cacheApp->get_relationship_a_opposition($topic, $opposition);
    }

    public function get_relationship_questions($topic)
    {
        return $this->cacheApp->get_relationship_questions($topic);
    }

    public function get_relationship_a_question($topic, $question)
    {
        return $this->cacheApp->get_relationship_a_question($topic, $question);
    }

    public function subtopics_get_relationship_questions($topic, $subtopic)
    {
        return $this->cacheApp->subtopics_get_relationship_questions($topic, $subtopic);
    }

    public function subtopics_get_relationship_a_question($topic, $subtopic, $question)
    {
        return $this->cacheApp->subtopics_get_relationship_a_question($topic, $subtopic, $question);
    }

    public function create_relationship_subtopic($request, $topic)
    {
        return $this->cacheApp->create_relationship_subtopic($request, $topic);
    }

    public function update_relationship_subtopic($request, $topic, $subtopic)
    {
        return $this->cacheApp->update_relationship_subtopic($request, $topic, $subtopic);
    }

    public function delete_relationship_subtopic($topic, $subtopic)
    {
        return $this->cacheApp->delete_relationship_subtopic($topic, $subtopic);
    }

    public function get_oppositions_available_of_topic($topic)
    {
        return $this->cacheApp->get_oppositions_available_of_topic($topic);
    }

    public function assign_opposition_with_subtopics_to_topic($request, $topic)
    {
        return $this->cacheApp->assign_opposition_with_subtopics_to_topic($request, $topic);
    }

    public function update_subtopics_opposition_by_topic($request, $topic, $opposition)
    {
        return $this->cacheApp->update_subtopics_opposition_by_topic($request, $topic, $opposition);
    }

    public function delete_opposition_by_topic($topic, $opposition)
    {
        $this->cacheApp->delete_opposition_by_topic($topic, $opposition);
    }

    public function topic_get_relationship_questions($topic)
    {
        return $this->cacheApp->topic_get_relationship_questions($topic);
    }

    public function topic_get_a_question($topic, $question)
    {
        return $this->cacheApp->topic_get_a_question($topic, $question);
    }

    public function topic_create_a_question($request, $topic)
    {
        return $this->cacheApp->topic_create_a_question($request, $topic);
    }

    public function topic_update_a_question($request, $topic, $question)
    {
        return $this->cacheApp->topic_update_a_question($request, $topic, $question);
    }

    public function topic_delete_a_question($topic, $question)
    {
        $this->cacheApp->topic_delete_a_question($topic, $question);
    }
}
