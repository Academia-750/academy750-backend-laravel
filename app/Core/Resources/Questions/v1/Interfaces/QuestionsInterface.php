<?php
namespace App\Core\Resources\Questions\v1\Interfaces;

use App\Models\Question;

interface QuestionsInterface
{
    public function subtopics_relationship_get_questions( $subtopic );
    public function subtopic_relationship_questions_read( $subtopic, $question );
    public function subtopic_relationship_questions_create( $request, $subtopic );
    public function subtopic_relationship_questions_update( $request, $subtopic, $question );
    public function subtopic_relationship_questions_delete( $subtopic, $question );

    public function topics_relationship_get_questions( $topic );
    public function topic_relationship_questions_read( $topic, $question );
    public function topic_relationship_questions_create( $request, $topic );
    public function topic_relationship_questions_update( $request, $topic, $question );
    public function topic_relationship_questions_delete( $topic, $question );
}
