<?php
namespace App\Core\Resources\Questions\v1;

use App\Imports\Api\v1\QuestionsImport;
use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Questions\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Questions\v1\Services\ActionsQuestionsRecords;
//use App\Imports\Api\Questions\v1\QuestionsImport;
use App\Exports\Api\Questions\v1\QuestionsExport;


class DBApp implements QuestionsInterface
{
    protected Question $model;

    public function __construct(Question $question ){
        $this->model = $question;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Question{
        try {

            DB::beginTransaction();
                $questionCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($questionCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $question ): \App\Models\Question{
        return $this->model->applyIncludes()->find($question->getRouteKey());
    }

    public function update( $request, $question ): \App\Models\Question{
        try {

            DB::beginTransaction();
                $question->name = $request->get('name');
                $question->save();
            DB::commit();

            return $this->model->applyIncludes()->find($question->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $question ): void{
        try {

            DB::beginTransaction();
                //$question->delete();
                ActionsQuestionsRecords::deleteRecord( $question );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('questions'));

            DB::commit();

            if (count($information) === 0) {
                $information[] = "No hay registros afectados";
            }

            return $information;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function export_records( $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse{
        if ($request->get('type') === 'pdf') {
            $domPDF = App::make('dompdf.wrapper');
            $questions = $this->model->query()->whereIn('id', $request->get('questions'))->get();
            $domPDF->loadView('resources.export.templates.pdf.questions', compact('questions'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-questions.pdf');
        }
        return Excel::download(new QuestionsExport($request->get('questions')), 'questions.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        $filesQuestions = $request->file('filesQuestions') ?? [];

        foreach ($filesQuestions as $file) {

            (
            new QuestionsImport(Auth::user(), $file->getClientOriginalName())
            )->import($file);

            //sleep(1);

        }
    }

    public function generate(){
        $questions_count = Question::isVisible()->applyFilters()->applySorts()->applyIncludes()->count();
        if($questions_count < request('filter')['take']){
            $questions = Question::where('is_visible', 'no')->get();
            $questions->map(fn (Question $question) => $question->update(['is_visible' => 'yes']));
        };

        $questions = Question::isVisible()->applyFilters()->applySorts()->applyIncludes()->get();
        $questions->map(fn (Question $question) => $question->update(['is_visible' => 'no']));
        return $questions;
    }
}
