<?php
namespace App\Core\Resources\Subtopics\v1\Interfaces;

use App\Models\Subtopic;

interface SubtopicsInterface
{
    public function index();
    public function create( $request );
    public function read( $subtopic );
    public function update($request, $subtopic );
    public function delete( $subtopic );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
    public function subtopic_get_relationship_questions( $subtopic );
    public function subtopic_get_a_question( $subtopic, $question );
    public function subtopic_create_a_question( $request, $subtopic );
    public function subtopic_update_a_question( $request, $subtopic, $question );
    public function subtopic_delete_a_question( $subtopic, $question );
}
