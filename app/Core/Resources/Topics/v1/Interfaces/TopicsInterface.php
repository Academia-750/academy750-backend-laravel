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

}
