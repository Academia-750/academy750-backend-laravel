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
}
