<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Opposition;
use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use App\Http\Resources\Api\Topic\v1\TopicResource;
use App\Models\TopicGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Topics\v1\SchemaJson;
class Authorizer implements TopicsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): TopicCollection
    {
        Gate::authorize('index', Topic::class );
        return $this->schemaJson->index();
    }
    public function get_topics_available_for_create_test($request): TopicCollection
    {
        return $this->schemaJson->get_topics_available_for_create_test($request);
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Topic::class );
        return $this->schemaJson->create($request);
    }

    public function read( $topic ): TopicResource
    {
        Gate::authorize('read', $topic );
        return $this->schemaJson->read( $topic );
    }

    public function update( $request, $topic ): TopicResource
    {
        Gate::authorize('update', $topic );
        return $this->schemaJson->update( $request, $topic );
    }

    public function delete( $topic ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $topic );
        return $this->schemaJson->delete( $topic );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Topic::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Topic::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Topic::class );
        return $this->schemaJson->import_records( $request );
    }

    public function get_relationship_subtopics($topic)
    {
        Gate::authorize('get_relationship_subtopics', $topic );
        return $this->schemaJson->get_relationship_subtopics($topic);
    }

    public function get_relationship_oppositions($topic)
    {
        Gate::authorize('get_relationship_oppositions', $topic );
        return $this->schemaJson->get_relationship_oppositions($topic);
    }

    public function get_relationship_a_subtopic($topic, $subtopic)
    {
        Gate::authorize('get_relationship_a_subtopic', $topic );
        return $this->schemaJson->get_relationship_a_subtopic($topic, $subtopic);
    }

    public function get_relationship_subtopics_by_opposition($topic, $opposition)
    {
        Gate::authorize('get_relationship_subtopics_by_opposition', [$topic, $opposition] );
        return $this->schemaJson->get_relationship_subtopics_by_opposition($topic, $opposition);
    }

    public function get_relationship_questions($topic)
    {
        Gate::authorize('get_relationship_questions', $topic );
        return $this->schemaJson->get_relationship_questions($topic);
    }

    public function get_relationship_a_question($topic, $question)
    {
        Gate::authorize('get_relationship_a_question', $topic );
        return $this->schemaJson->get_relationship_a_question($topic, $question);
    }

    public function subtopics_get_relationship_questions($topic, $subtopic)
    {
        Gate::authorize('subtopics_get_relationship_questions', [$topic, $subtopic] );
        return $this->schemaJson->subtopics_get_relationship_questions($topic, $subtopic);
    }

    public function subtopics_get_relationship_a_question($topic, $subtopic, $question)
    {
        Gate::authorize('subtopics_get_relationship_a_question', [$topic, $subtopic] );
        return $this->schemaJson->subtopics_get_relationship_a_question($topic, $subtopic, $question);
    }

    public function create_relationship_subtopic($request, $topic)
    {
        Gate::authorize('create_relationship_subtopic', $topic );
        return $this->schemaJson->create_relationship_subtopic($request, $topic);
    }

    public function update_relationship_subtopic($request, $topic, $subtopic)
    {
        Gate::authorize('update_relationship_subtopic', [$topic, $subtopic] );
        return $this->schemaJson->update_relationship_subtopic($request, $topic, $subtopic);
    }

    public function delete_relationship_subtopic($topic, $subtopic)
    {
        Gate::authorize('delete_relationship_subtopic', [$topic, $subtopic] );
        return $this->schemaJson->delete_relationship_subtopic($topic, $subtopic);
    }

    public function get_oppositions_available_of_topic($topic)
    {
        Gate::authorize('get_oppositions_available_of_topic', $topic );
        return $this->schemaJson->get_oppositions_available_of_topic($topic);
    }

    public function assign_opposition_with_subtopics_to_topic($request, $topic)
    {
        Gate::authorize('assign_opposition_with_subtopics_to_topic', $topic );
        return $this->schemaJson->assign_opposition_with_subtopics_to_topic($request, $topic);
    }

    public function update_subtopics_opposition_by_topic($request, $topic, $opposition)
    {
        Gate::authorize('update_subtopics_opposition_by_topic', [$topic, $opposition] );
        return $this->schemaJson->update_subtopics_opposition_by_topic($request, $topic, $opposition);
    }

    public function delete_opposition_by_topic($topic, $opposition)
    {
        Gate::authorize('delete_opposition_by_topic', [$topic, $opposition] );
        return $this->schemaJson->delete_opposition_by_topic($topic, $opposition);
    }

    public function topic_get_relationship_questions($topic)
    {
        Gate::authorize('topic_get_relationship_questions', $topic );
        return $this->schemaJson->topic_get_relationship_questions($topic);
    }

    public function topic_get_a_question($topic, $question)
    {
        Gate::authorize('topic_get_a_question', [$topic, $question] );
        return $this->schemaJson->topic_get_a_question($topic, $question);
    }

    public function topic_create_a_question($request, $topic)
    {
        Gate::authorize('topic_create_a_question', $topic );
        return $this->schemaJson->topic_create_a_question($request, $topic);
    }

    public function topic_update_a_question($request, $topic, $question)
    {
        Gate::authorize('topic_update_a_question', [$topic, $question] );
        return $this->schemaJson->topic_update_a_question($request, $topic, $question);
    }

    public function topic_delete_a_question($topic, $question)
    {
        Gate::authorize('topic_delete_a_question', [$topic, $question] );
        return $this->schemaJson->topic_delete_a_question($topic, $question);
    }

    public function topic_relationship_questions()
    {
        Gate::authorize('topic_relationship_questions', Topic::class );
        return $this->schemaJson->topic_relationship_questions();
    }

    public function import_subtopics_by_topics($request)
    {
        Gate::authorize('import_records', Topic::class );
        return $this->schemaJson->import_subtopics_by_topics($request);
    }

    public function topics_get_worst_topics_of_student()
    {
        return $this->schemaJson->topics_get_worst_topics_of_student();
    }
}
