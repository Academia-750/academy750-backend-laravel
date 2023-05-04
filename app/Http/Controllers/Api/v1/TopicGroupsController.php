<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TopicGroupsController extends Controller
{
    protected TopicGroupsInterface $topicGroupsInterface;

    public function __construct(TopicGroupsInterface $topicGroupsInterface ){
        $this->topicGroupsInterface = $topicGroupsInterface;
    }

    public function index(){
        return $this->topicGroupsInterface->index();
    }

    public function read(TopicGroup $topic_group){
        return $this->topicGroupsInterface->read( $topic_group );
    }
}
