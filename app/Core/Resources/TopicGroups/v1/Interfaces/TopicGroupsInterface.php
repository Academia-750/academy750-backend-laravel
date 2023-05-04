<?php
namespace App\Core\Resources\TopicGroups\v1\Interfaces;

use App\Models\TopicGroup;

interface TopicGroupsInterface
{
    public function index();
    public function read( $topic_group );

}
