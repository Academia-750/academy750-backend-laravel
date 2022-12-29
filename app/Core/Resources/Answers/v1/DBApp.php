<?php
namespace App\Core\Resources\Answers\v1;

use App\Models\Answer;
use App\Core\Resources\Answers\v1\Interfaces\AnswersInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Answers\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Answers\v1\Services\ActionsAnswersRecords;
//use App\Imports\Api\Answers\v1\AnswersImport;
use App\Exports\Api\Answers\v1\AnswersExport;


class DBApp implements AnswersInterface
{
    protected Answer $model;

    public function __construct(Answer $answer ){
        $this->model = $answer;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Answer{
        try {

            DB::beginTransaction();
                $answerCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($answerCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $answer ): \App\Models\Answer{
        return $this->model->applyIncludes()->find($answer->getRouteKey());
    }

    public function update( $request, $answer ): \App\Models\Answer{
        try {

            DB::beginTransaction();
                $answer->name = $request->get('name');
                $answer->save();
            DB::commit();

            return $this->model->applyIncludes()->find($answer->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $answer ): void{
        try {

            DB::beginTransaction();
                //$answer->delete();
                ActionsAnswersRecords::deleteRecord( $answer );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('answers'));

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
            $answers = $this->model->query()->whereIn('id', $request->get('answers'))->get();
            $domPDF->loadView('resources.export.templates.pdf.answers', compact('answers'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-answers.pdf');
        }
        return Excel::download(new AnswersExport($request->get('answers')), 'answers.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new AnswersImport(Auth::user()))->import($request->file('answers'));
    }

}
