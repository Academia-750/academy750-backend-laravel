<?php
namespace App\Core\Resources\TopicGroups\v1\Interfaces;

use App\Models\TopicGroup;

interface TopicGroupsInterface
{
    public function index();
    public function create( $request );
    public function read( $topic_group );
    public function update($request, $topic_group );
    public function delete( $topic_group );
    public function action_for_multiple_records( $request );
    public function export_records( $request );
    public function import_records( $request );
}
