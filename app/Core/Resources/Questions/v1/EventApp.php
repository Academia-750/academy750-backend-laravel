<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Questions\v1\CacheApp;
class EventApp implements QuestionsInterface
{
    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {
        return $this->cacheApp->subtopics_relationship_get_questions($subtopic);
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        return $this->cacheApp->subtopic_relationship_questions_read($subtopic, $question);
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        return $this->cacheApp->subtopic_relationship_questions_create($request, $subtopic);
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        return $this->cacheApp->subtopic_relationship_questions_update($request, $subtopic, $question);
    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        return $this->cacheApp->subtopic_relationship_questions_delete($subtopic, $question);
    }

    public function topics_relationship_get_questions($topic)
    {
        return $this->cacheApp->topics_relationship_get_questions($topic);
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        \Log::debug('EventApp->topic_relationship_questions_read');

        return $this->cacheApp->topic_relationship_questions_read($topic, $question);
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        return $this->cacheApp->topic_relationship_questions_create($request, $topic);
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        return $this->cacheApp->topic_relationship_questions_update($request, $topic, $question);
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        return $this->cacheApp->topic_relationship_questions_delete($topic, $question);
    }

    public function claim_question_mail($request)
    {
        return $this->cacheApp->claim_question_mail($request);
    }

    public function import_records($request)
    {
        $this->cacheApp->import_records($request);
    }

    public function set_mode_edit_question($request, $question)
    {
        $this->cacheApp->set_mode_edit_question($request, $question);
    }
}
