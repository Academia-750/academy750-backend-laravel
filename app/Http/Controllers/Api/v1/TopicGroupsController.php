<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\TopicGroups\CreateTopicGroupRequest;
use App\Http\Requests\Api\v1\TopicGroups\UpdateTopicGroupRequest;
use App\Http\Requests\Api\v1\TopicGroups\ActionForMassiveSelectionTopicGroupsRequest;
use App\Http\Requests\Api\v1\TopicGroups\ExportTopicGroupsRequest;
use App\Http\Requests\Api\v1\TopicGroups\ImportTopicGroupsRequest;

class TopicGroupsController extends Controller
{
    protected TopicGroupsInterface $topicGroupsInterface;

    public function __construct(TopicGroupsInterface $topicGroupsInterface ){
        $this->topicGroupsInterface = $topicGroupsInterface;
    }

    public function index(){
        return $this->topicGroupsInterface->index();
    }

    public function create(CreateTopicGroupRequest $request){
        return $this->topicGroupsInterface->create($request);
    }

    public function read(TopicGroup $topic_group){
        return $this->topicGroupsInterface->read( $topic_group );
    }

    public function update(UpdateTopicGroupRequest $request, TopicGroup $topic_group){
        return $this->topicGroupsInterface->update( $request, $topic_group );
    }

    public function delete(TopicGroup $topic_group){
        return $this->topicGroupsInterface->delete( $topic_group );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionTopicGroupsRequest $request): string{
        return $this->topicGroupsInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportTopicGroupsRequest $request){
        return $this->topicGroupsInterface->export_records( $request );
    }

    public function import_records(ImportTopicGroupsRequest $request){
        return $this->topicGroupsInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/topic_group.csv', 'template_import_topic_group');
    }
}
