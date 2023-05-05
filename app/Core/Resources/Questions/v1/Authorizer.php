<?php
namespace App\Core\Resources\Questions\v1;

use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Support\Facades\Gate;

class Authorizer implements QuestionsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {
        abort_if(!$subtopic->isAvailable(), 403);
        return $this->schemaJson->subtopics_relationship_get_questions($subtopic);
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        abort_if(!$subtopic->questions()->firstWhere('id', '=', $question->getRouteKey()) || !$subtopic->isAvailable() || !$question->isVisible(), 403);

        return $this->schemaJson->subtopic_relationship_questions_read($subtopic, $question);
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        abort_if(!$subtopic->isAvailable(), 403);
        return $this->schemaJson->subtopic_relationship_questions_create($request, $subtopic);
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        abort_if(!$subtopic->questions()->firstWhere('id', '=', $question->getRouteKey()) || !$subtopic->isAvailable() || $question->tests()->count() > 0 || !$question->isVisible(), 403);

        return $this->schemaJson->subtopic_relationship_questions_update($request, $subtopic, $question);
    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {

        abort_if(
            !$subtopic->questions()->firstWhere('id', '=', $question->getRouteKey()) || !$subtopic->isAvailable() || !$question->isVisible()
            , 403);

        return $this->schemaJson->subtopic_relationship_questions_delete($subtopic, $question);
    }

    public function topics_relationship_get_questions($topic)
    {
        //abort_if(!$topic->isAvailable(), 403);

        return $this->schemaJson->topics_relationship_get_questions($topic);
    }

    public function topic_relationship_questions_read($topic, $question)
    {

        abort_if(!$topic->questions()->firstWhere('id', '=', $question->getRouteKey()) || !$topic->isAvailable() || !$question->isVisible() || !$question->isVisible(), 403);

        return $this->schemaJson->topic_relationship_questions_read($topic, $question);
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        abort_if(!$topic->isAvailable(), 403);
        return $this->schemaJson->topic_relationship_questions_create($request, $topic);
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        abort_if(!$topic->questions()->firstWhere('id', '=', $question->getRouteKey()) || $question->tests()->count() > 0 || !$topic->isAvailable() || !$question->isVisible(), 403);

        return $this->schemaJson->topic_relationship_questions_update($request, $topic, $question);
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        abort_if(
            !$topic->questions()->firstWhere('id', '=', $question->getRouteKey()) || !$topic->isAvailable() || !$question->isVisible()
            ,403);

        return $this->schemaJson->topic_relationship_questions_delete($topic, $question);
    }

    public function claim_question_mail($request)
    {
        return $this->schemaJson->claim_question_mail($request);
    }

    public function import_records($request)
    {
        $this->schemaJson->import_records($request);
    }

    public function set_mode_edit_question($request, $question)
    {
        abort_if(!$question->isVisible() || $question->tests()->count(), 403);

        return $this->schemaJson->set_mode_edit_question($request, $question);
    }
}
