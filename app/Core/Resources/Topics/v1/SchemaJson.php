<?php
namespace App\Core\Resources\Topics\v1;

use App\Http\Resources\Api\Opposition\v1\OppositionCollection;
use App\Http\Resources\Api\Opposition\v1\OppositionResource;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Question\v1\QuestionResource;
use App\Http\Resources\Api\Subtopic\v1\SubtopicCollection;
use App\Http\Resources\Api\Subtopic\v1\SubtopicResource;
use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use App\Http\Resources\Api\Topic\v1\TopicResource;
use App\Core\Resources\Topics\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TopicsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): TopicCollection
    {
        return TopicCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return TopicResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $topic ): TopicResource
    {
        return TopicResource::make(
            $this->eventApp->read( $topic )
        );
    }

    public function update( $request, $topic ): TopicResource
    {
        return TopicResource::make(
            $this->eventApp->update( $request, $topic )
        );
    }

    public function delete( $topic ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $topic );
        return response()->noContent();
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'information' => $this->eventApp->action_for_multiple_records( $request )
        ], 200);
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->export_records( $request );

        return response()->json([
            'message' => "Proceso de exportación iniciada"
        ], 200);
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->import_records( $request );

        return response()->json([
            'message' => "Proceso de importación iniciada"
        ], 200);
    }

    public function get_relationship_subtopics($topic)
    {
        return SubtopicCollection::make(
            $this->eventApp->get_relationship_subtopics($topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function get_relationship_oppositions($topic)
    {

        return OppositionCollection::make(
            $this->eventApp->get_relationship_oppositions($topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function get_relationship_a_subtopic($topic, $subtopic)
    {
        return SubtopicResource::make(
            $this->eventApp->get_relationship_a_subtopic($topic, $subtopic)
        );
    }

    public function get_relationship_a_opposition($topic, $opposition)
    {
        return SubtopicCollection::make(
            $this->eventApp->get_relationship_a_opposition($topic, $opposition)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic),
                'opposition' => OppositionResource::make($opposition)
            ]
        ]);
    }

    public function get_relationship_questions($topic)
    {
        return QuestionCollection::make(
            $this->eventApp->get_relationship_questions($topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function get_relationship_a_question($topic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->get_relationship_a_question($topic, $question)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function subtopics_get_relationship_questions($topic, $subtopic)
    {
        return QuestionCollection::make(
            $this->eventApp->subtopics_get_relationship_questions($topic, $subtopic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function subtopics_get_relationship_a_question($topic, $subtopic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->subtopics_get_relationship_a_question($topic, $subtopic, $question)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function create_relationship_subtopic($request, $topic)
    {
        return SubtopicResource::make(
            $this->eventApp->create_relationship_subtopic($request, $topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function update_relationship_subtopic($request, $topic, $subtopic)
    {
        return SubtopicResource::make(
            $this->eventApp->update_relationship_subtopic($request, $topic, $subtopic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function delete_relationship_subtopic($topic, $subtopic)
    {
        $this->eventApp->delete_relationship_subtopic($topic, $subtopic);
        return response()->noContent();
    }

    public function get_oppositions_available_of_topic($topic)
    {
        return OppositionCollection::make(
            $this->eventApp->get_oppositions_available_of_topic($topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function assign_opposition_with_subtopics_to_topic($request, $topic)
    {
        return TopicResource::make(
            $this->eventApp->assign_opposition_with_subtopics_to_topic($request, $topic)
        );
    }

    public function update_subtopics_opposition_by_topic($request, $topic, $opposition)
    {
        return TopicResource::make(
            $this->eventApp->update_subtopics_opposition_by_topic($request, $topic, $opposition)
        );
    }

    public function delete_opposition_by_topic($topic, $opposition)
    {
        $this->eventApp->delete_opposition_by_topic($topic, $opposition);
        return response()->noContent();
    }

    public function topic_get_relationship_questions($topic)
    {
        return TopicResource::make(
            $this->eventApp->topic_get_relationship_questions($topic)
        );
    }

    public function topic_get_a_question($topic, $question)
    {
        return TopicResource::make(
            $this->eventApp->topic_get_a_question($topic, $question)
        );
    }

    public function topic_create_a_question($request, $topic)
    {
        return TopicResource::make(
            $this->eventApp->topic_create_a_question($request, $topic)
        );
    }

    public function topic_update_a_question($request, $topic, $question)
    {
        return TopicResource::make(
            $this->eventApp->topic_update_a_question($request, $topic, $question)
        );
    }

    public function topic_delete_a_question($topic, $question)
    {
        return TopicResource::make(
            $this->eventApp->topic_delete_a_question($topic, $question)
        );
    }
}
