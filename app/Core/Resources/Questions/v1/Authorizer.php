<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Question\v1\QuestionResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Questions\v1\SchemaJson;
class Authorizer implements QuestionsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {
        //Gate::authorize('subtopics_relationship_get_questions', [Question::class, $subtopic] );
        return $this->schemaJson->subtopics_relationship_get_questions($subtopic);
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        //Gate::authorize('subtopic_relationship_questions_read', [$subtopic, $question] );
        if (!$subtopic->questions()->firstWhere('id', '=', $question->getRouteKey())) {
            abort(403);
        }

        return $this->schemaJson->subtopic_relationship_questions_read($subtopic, $question);
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        //Gate::authorize('subtopic_relationship_questions_create', $subtopic );
        return $this->schemaJson->subtopic_relationship_questions_create($request, $subtopic);
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        //Gate::authorize('subtopic_relationship_questions_update', [$subtopic, $question] );
        if (!$subtopic->questions()->firstWhere('id', '=', $question->getRouteKey())) {
            abort(403);
        }
        return $this->schemaJson->subtopic_relationship_questions_update($request, $subtopic, $question);
    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        //Gate::authorize('subtopic_relationship_questions_delete', [$subtopic, $question] );
        if (!$subtopic->questions()->firstWhere('id', '=', $question->getRouteKey())) {
            abort(403);
        }
        return $this->schemaJson->subtopic_relationship_questions_delete($subtopic, $question);
    }

    public function topics_relationship_get_questions($topic)
    {
        ////Gate::authorize('topics_relationship_get_questions', [Question::class, $topic] );
        return $this->schemaJson->topics_relationship_get_questions($topic);
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        //Gate::authorize('topic_relationship_questions_read', [$topic, $question] );
        if (!$topic->questions()->firstWhere('id', '=', $question->getRouteKey())) {
            abort(403);
        }
        return $this->schemaJson->topic_relationship_questions_read($topic, $question);
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        //Gate::authorize('topic_relationship_questions_create', $topic );
        return $this->schemaJson->topic_relationship_questions_create($request, $topic);
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        //Gate::authorize('topic_relationship_questions_update', [$topic, $question] );
        if (!$topic->questions()->firstWhere('id', '=', $question->getRouteKey())) {
            abort(403);
        }
        return $this->schemaJson->topic_relationship_questions_update($request, $topic, $question);
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        //Gate::authorize('topic_relationship_questions_delete', [$topic, $question] );
        if (!$topic->questions()->firstWhere('id', '=', $question->getRouteKey())) {
            abort(403);
        }
        return $this->schemaJson->topic_relationship_questions_delete($topic, $question);
    }

}
