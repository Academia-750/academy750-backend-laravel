<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Core\Resources\Questions\v1\DBApp;
class CacheApp implements QuestionsInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {
        /*$nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'subtopics.question.get.all' : $nameCache = "subtopics.question.get.all" . json_encode( request()->query() );

        return Cache::store('redis')->tags('question')->rememberForever($nameCache, function () use ($subtopic) {
            return $this->dbApp->subtopics_relationship_get_questions($subtopic);
        });*/

        return $this->dbApp->subtopics_relationship_get_questions($subtopic);
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        /*return Cache::store('redis')->tags('question')->rememberForever("subtopics.{$subtopic->getRouteKey()}.question.find.{$question->getRouteKey()}", function () use ( $subtopic, $question ) {
            return $this->dbApp->subtopic_relationship_questions_read($subtopic, $question);
        });*/

        return $this->dbApp->subtopic_relationship_questions_read($subtopic, $question);
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        //Cache::store('redis')->tags('question')->flush();
        //Cache::store('redis')->tags('answer')->flush();
        return $this->dbApp->subtopic_relationship_questions_create($request, $subtopic);
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        //Cache::store('redis')->tags('question')->flush();
        //Cache::store('redis')->tags('answer')->flush();
        return $this->dbApp->subtopic_relationship_questions_update($request, $subtopic, $question);
    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        //Cache::store('redis')->tags('question')->flush();
        //Cache::store('redis')->tags('answer')->flush();
        return $this->dbApp->subtopic_relationship_questions_delete($subtopic, $question);
    }

    public function topics_relationship_get_questions($topic)
    {
        /*$nameCache = '';

        ( empty(request()->query()) ) ? $nameCache = 'topics.question.get.all' : $nameCache = "topics.question.get.all" . json_encode( request()->query() );

        return Cache::store('redis')->tags('question')->rememberForever($nameCache, function () use ($topic) {
            return $this->dbApp->topics_relationship_get_questions($topic);
        });*/

        return $this->dbApp->topics_relationship_get_questions($topic);
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        /*return Cache::store('redis')->tags('question')->rememberForever("topics.{$topic->getRouteKey()}.question.find.{$question->getRouteKey()}", function () use ( $topic, $question ) {
            return $this->dbApp->topic_relationship_questions_read($topic, $question);
        });*/

        return $this->dbApp->topic_relationship_questions_read($topic, $question);

    }

    public function topic_relationship_questions_create($request, $topic)
    {
        //Cache::store('redis')->tags('question')->flush();
        //Cache::store('redis')->tags('answer')->flush();
        return $this->dbApp->topic_relationship_questions_create($request, $topic);
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        //Cache::store('redis')->tags('question')->flush();
        //Cache::store('redis')->tags('answer')->flush();
        return $this->dbApp->topic_relationship_questions_update($request, $topic, $question);
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        //Cache::store('redis')->tags('question')->flush();
        //Cache::store('redis')->tags('answer')->flush();
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
