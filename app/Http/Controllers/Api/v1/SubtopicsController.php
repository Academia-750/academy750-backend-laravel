<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Subtopic;
use App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Subtopics\CreateSubtopicRequest;
use App\Http\Requests\Api\v1\Subtopics\UpdateSubtopicRequest;
use App\Http\Requests\Api\v1\Subtopics\ActionForMassiveSelectionSubtopicsRequest;
use App\Http\Requests\Api\v1\Subtopics\ExportSubtopicsRequest;
use App\Http\Requests\Api\v1\Subtopics\ImportSubtopicsRequest;

class SubtopicsController extends Controller
{
    protected SubtopicsInterface $subtopicsInterface;

    public function __construct(SubtopicsInterface $subtopicsInterface ){
        $this->subtopicsInterface = $subtopicsInterface;
    }

    public function index(){
        return $this->subtopicsInterface->index();
    }

    public function create(CreateSubtopicRequest $request){
        return $this->subtopicsInterface->create($request);
    }

    public function read(Subtopic $subtopic){
        return $this->subtopicsInterface->read( $subtopic );
    }

    public function update(UpdateSubtopicRequest $request, Subtopic $subtopic){
        return $this->subtopicsInterface->update( $request, $subtopic );
    }

    public function delete(Subtopic $subtopic){
        return $this->subtopicsInterface->delete( $subtopic );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionSubtopicsRequest $request): string{
        return $this->subtopicsInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportSubtopicsRequest $request){
        return $this->subtopicsInterface->export_records( $request );
    }

    public function import_records(ImportSubtopicsRequest $request){
        return $this->subtopicsInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/subtopic.csv', 'template_import_subtopic');
    }
}
