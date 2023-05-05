<?php
namespace App\Core\Resources\Questions\v1;

use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
class CacheApp implements QuestionsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {
        return $this->dbApp->subtopics_relationship_get_questions($subtopic);
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        return $this->dbApp->subtopic_relationship_questions_read($subtopic, $question);
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        return $this->dbApp->subtopic_relationship_questions_create($request, $subtopic);
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        return $this->dbApp->subtopic_relationship_questions_update($request, $subtopic, $question);
    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        return $this->dbApp->subtopic_relationship_questions_delete($subtopic, $question);
    }

    public function topics_relationship_get_questions($topic)
    {
        return $this->dbApp->topics_relationship_get_questions($topic);
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        return $this->dbApp->topic_relationship_questions_read($topic, $question);
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        return $this->dbApp->topic_relationship_questions_create($request, $topic);
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        return $this->dbApp->topic_relationship_questions_update($request, $topic, $question);
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        return $this->dbApp->topic_relationship_questions_delete($topic, $question);
    }

    public function claim_question_mail($request)
    {
        return $this->dbApp->claim_question_mail($request);
    }

    public function import_records($request)
    {
        $this->dbApp->import_records($request);
    }

    public function set_mode_edit_question($request, $question)
    {
        $this->dbApp->set_mode_edit_question($request, $question);
    }
}
