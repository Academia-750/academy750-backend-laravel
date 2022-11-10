<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Topics\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Topics\v1\Services\ActionsTopicsRecords;
//use App\Imports\Api\Topics\v1\TopicsImport;
use App\Exports\Api\Topics\v1\TopicsExport;


class DBApp implements TopicsInterface
{
    protected Topic $model;

    public function __construct(Topic $topic ){
        $this->model = $topic;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Topic{
        try {

            DB::beginTransaction();
                $topicCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($topicCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $topic ): \App\Models\Topic{
        return $this->model->applyIncludes()->find($topic->getRouteKey());
    }

    public function update( $request, $topic ): \App\Models\Topic{
        try {

            DB::beginTransaction();
                $topic->name = $request->get('name');
                $topic->save();
            DB::commit();

            return $this->model->applyIncludes()->find($topic->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $topic ): void{
        try {

            DB::beginTransaction();
                //$topic->delete();
                ActionsTopicRecords::deleteTopic( $topic );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('$topics'));

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
            $topics = $this->model->query()->whereIn('id', $request->get('topics'))->get();
            $domPDF->loadView('resources.export.templates.pdf.topics', compact('topics'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-topics.pdf');
        }
        return Excel::download(new TopicsExport($request->get('topics')), 'topics.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new TopicsImport(Auth::user()))->import($request->file('topics'));
    }

}
