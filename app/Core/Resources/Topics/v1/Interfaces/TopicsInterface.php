<?php
namespace App\Core\Resources\Topics\v1\Interfaces;

use App\Models\Topic;

interface TopicsInterface
{
    public function index();
    public function create( $request );
    public function read( $topic );
    public function update($request, $topic );
    public function delete( $topic );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
    public function get_relationship_subtopics( $topic );
    public function get_relationship_oppositions( $topic );
    public function get_relationship_a_subtopic( $topic, $subtopic );
    public function get_relationship_a_opposition( $topic, $opposition );
    public function get_relationship_questions( $topic );
    public function get_relationship_a_question( $topic, $question );
    public function subtopics_get_relationship_questions($topic, $subtopic);
    public function subtopics_get_relationship_a_question($topic, $subtopic, $question);
    public function create_relationship_subtopic($request, $topic);
    public function update_relationship_subtopic($request, $topic, $subtopic);
    public function delete_relationship_subtopic($topic, $subtopic);
    public function get_oppositions_available_of_topic($topic);
    public function assign_opposition_with_subtopics_to_topic($request, $topic);
    public function update_subtopics_opposition_by_topic($request, $topic, $opposition);
    public function delete_opposition_by_topic($topic, $opposition);
    public function topic_get_relationship_questions( $topic );
    public function topic_get_a_question( $topic, $question );
    public function topic_create_a_question( $request, $topic );
    public function topic_update_a_question( $request, $topic, $question );
    public function topic_delete_a_question( $topic, $question );
    public function topic_relationship_questions();
}
