<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Topics\v1\DBApp;
class CacheApp implements TopicsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){

        $nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'topic.get.all' : $nameCache = json_encode( request()->query() );

        return Cache::store('redis')->tags('topic')->rememberForever($nameCache, function () {
            return $this->dbApp->index();
        });

    }

    public function create( $request ){

        Cache::store('redis')->tags('topic')->flush();

        return $this->dbApp->create( $request );
    }

    public function read( $topic ){

        return Cache::store('redis')->tags('topic')->rememberForever("topic.find.".$topic->getRouteKey(), function () use ( $topic ) {
            return $this->dbApp->read( $topic );
        });
    }

    public function update( $request, $topic ){

        Cache::store('redis')->tags('topic')->flush();

        return $this->dbApp->update( $request, $topic );
    }

    public function delete( $topic ): void{

        Cache::store('redis')->tags('topic')->flush();
        $this->dbApp->delete( $topic );
    }

    public function action_for_multiple_records( $request ): array{

        Cache::store('redis')->tags('topic')->flush();

        return $this->dbApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        $this->dbApp->export_records( $request );
    }

    public function import_records( $request ): void{
        Cache::store('redis')->tags('topic')->flush();
        $this->dbApp->import_records( $request );
    }

    public function get_relationship_subtopics($topic)
    {
        return $this->dbApp->get_relationship_subtopics( $topic );
    }

    public function get_relationship_oppositions($topic)
    {
        return $this->dbApp->get_relationship_oppositions( $topic );
    }

    public function get_relationship_a_subtopic($topic, $subtopic)
    {
        return $this->dbApp->get_relationship_a_subtopic($topic, $subtopic);
    }

    public function get_relationship_a_opposition($topic, $opposition)
    {
        return $this->dbApp->get_relationship_a_opposition( $topic, $opposition );
    }

    public function get_relationship_questions($topic)
    {
        return $this->dbApp->get_relationship_questions($topic);
    }

    public function get_relationship_a_question($topic, $question)
    {
        return $this->dbApp->get_relationship_a_question($topic, $question);
    }

    public function subtopics_get_relationship_questions($topic, $subtopic)
    {
        return $this->dbApp->subtopics_get_relationship_questions($topic, $subtopic);
    }

    public function subtopics_get_relationship_a_question($topic, $subtopic, $question)
    {
        return $this->dbApp->subtopics_get_relationship_a_question($topic, $subtopic, $question);
    }

    public function create_relationship_subtopic($request, $topic)
    {
        return $this->dbApp->create_relationship_subtopic($request, $topic);
    }

    public function update_relationship_subtopic($request, $topic, $subtopic)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        return $this->dbApp->update_relationship_subtopic($request, $topic, $subtopic);
    }

    public function delete_relationship_subtopic($topic, $subtopic)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        return $this->dbApp->delete_relationship_subtopic($topic, $subtopic);
    }

    public function get_oppositions_available_of_topic($topic)
    {
        return $this->dbApp->get_oppositions_available_of_topic($topic);
    }

    public function assign_opposition_with_subtopics_to_topic($request, $topic)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        return $this->dbApp->assign_opposition_with_subtopics_to_topic($request, $topic);
    }
    public function update_subtopics_opposition_by_topic($request, $topic, $opposition)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        return $this->dbApp->update_subtopics_opposition_by_topic($request, $topic, $opposition);
    }

    public function delete_opposition_by_topic($topic, $opposition)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        $this->dbApp->delete_opposition_by_topic($topic, $opposition);
    }

    public function topic_get_relationship_questions($topic)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        $this->dbApp->topic_get_relationship_questions($topic);
    }

    public function topic_get_a_question($topic, $question)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        $this->dbApp->topic_get_a_question($topic, $question);
    }

    public function topic_create_a_question($request, $topic)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        $this->dbApp->topic_create_a_question($request, $topic);
    }

    public function topic_update_a_question($request, $topic, $question)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        $this->dbApp->topic_update_a_question($request, $topic, $question);
    }

    public function topic_delete_a_question($topic, $question)
    {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('subtopic')->flush();
        Cache::store('redis')->tags('opposition')->flush();
        $this->dbApp->topic_delete_a_question($topic, $question);
    }
}
