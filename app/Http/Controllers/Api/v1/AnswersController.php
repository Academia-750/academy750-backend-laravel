<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Answer;
use App\Core\Resources\Answers\v1\Interfaces\AnswersInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Answers\CreateAnswerRequest;
use App\Http\Requests\Api\v1\Answers\UpdateAnswerRequest;
use App\Http\Requests\Api\v1\Answers\ActionForMassiveSelectionAnswersRequest;
use App\Http\Requests\Api\v1\Answers\ExportAnswersRequest;
use App\Http\Requests\Api\v1\Answers\ImportAnswersRequest;

class AnswersController extends Controller
{
    protected AnswersInterface $answersInterface;

    public function __construct(AnswersInterface $answersInterface ){
        $this->answersInterface = $answersInterface;
    }

    public function index(){
        return $this->answersInterface->index();
    }

    public function create(CreateAnswerRequest $request){
        return $this->answersInterface->create($request);
    }

    public function read(Answer $answer){
        return $this->answersInterface->read( $answer );
    }

    public function update(UpdateAnswerRequest $request, Answer $answer){
        return $this->answersInterface->update( $request, $answer );
    }

    public function delete(Answer $answer){
        return $this->answersInterface->delete( $answer );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionAnswersRequest $request): string{
        return $this->answersInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportAnswersRequest $request){
        return $this->answersInterface->export_records( $request );
    }

    public function import_records(ImportAnswersRequest $request){
        return $this->answersInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/answer.csv', 'template_import_answer');
    }
}
