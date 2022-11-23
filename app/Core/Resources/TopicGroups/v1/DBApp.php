<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\TopicGroups\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\TopicGroups\v1\Services\ActionsTopicGroupsRecords;
//use App\Imports\Api\TopicGroups\v1\TopicGroupsImport;
use App\Exports\Api\TopicGroups\v1\TopicGroupsExport;


class DBApp implements TopicGroupsInterface
{
    protected TopicGroup $model;

    public function __construct(TopicGroup $topic_group ){
        $this->model = $topic_group;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\TopicGroup{
        try {

            DB::beginTransaction();
                $topic_groupCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($topic_groupCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $topic_group ): \App\Models\TopicGroup{
        return $this->model->applyIncludes()->find($topic_group->getRouteKey());
    }

    public function update( $request, $topic_group ): \App\Models\TopicGroup{
        try {

            DB::beginTransaction();
                $topic_group->name = $request->get('name');
                $topic_group->save();
            DB::commit();

            return $this->model->applyIncludes()->find($topic_group->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $topic_group ): void{
        try {

            DB::beginTransaction();
                //$topic_group->delete();
                ActionsTopicGroupsRecords::deleteRecord( $topic_group );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('topic_groups'));

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
            $topic_groups = $this->model->query()->whereIn('id', $request->get('topic-groups'))->get();
            $domPDF->loadView('resources.export.templates.pdf.topic-groups', compact('topic_groups'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-topic-groups.pdf');
        }
        return Excel::download(new TopicGroupsExport($request->get('topic-groups')), 'topic-groups.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new TopicGroupsImport(Auth::user()))->import($request->file('topic-groups'));
    }

}
