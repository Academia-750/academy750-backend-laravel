<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Questions\CreateQuestionRequest;
use App\Http\Requests\Api\v1\Questions\UpdateQuestionRequest;
use App\Http\Requests\Api\v1\Questions\ActionForMassiveSelectionQuestionsRequest;
use App\Http\Requests\Api\v1\Questions\ExportQuestionsRequest;
use App\Http\Requests\Api\v1\Questions\ImportQuestionsRequest;

class QuestionsController extends Controller
{
    protected QuestionsInterface $questionsInterface;

    public function __construct(QuestionsInterface $questionsInterface ){
        $this->questionsInterface = $questionsInterface;
    }

    public function index(){
        return $this->questionsInterface->generate();
    }

    public function create(CreateQuestionRequest $request){
        return $this->questionsInterface->create($request);
    }

    public function read(Question $question){
        return $this->questionsInterface->read( $question );
    }

    public function update(UpdateQuestionRequest $request, Question $question){
        return $this->questionsInterface->update( $request, $question );
    }

    public function delete(Question $question){
        return $this->questionsInterface->delete( $question );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionQuestionsRequest $request): string{
        return $this->questionsInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportQuestionsRequest $request){
        return $this->questionsInterface->export_records( $request );
    }

    public function import_records(ImportQuestionsRequest $request){
        return $this->questionsInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/question.csv', 'template_import_question');
    }

    public function generate(){
        return $this->questionsInterface->generate();
    }
}
