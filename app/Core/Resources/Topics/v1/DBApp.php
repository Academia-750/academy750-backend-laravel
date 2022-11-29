<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Subtopic;
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
                    'name' => $request->get('name'),
                    'topic_group_id' => $request->get('topic-group-id')
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
                $topic->name = $request->get('name') ?? $topic->name;
                $topic->topic_group_id = $request->get('topic-group-id') ?? $topic->topic_group_id;
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
                ActionsTopicsRecords::deleteRecord( $topic );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('topics'));

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

    public function get_relationship_subtopics($topic)
    {
        return $topic->subtopics()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_relationship_oppositions($topic)
    {
        return $topic->oppositions()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_relationship_a_opposition($topic, $opposition)
    {
        $subtopics_id = [];

        foreach ($opposition->subtopics as $opposition_subtopic) {
            $subtopics_id_of_topic = $topic->subtopics->pluck('id')->toArray();
            if (in_array($subtopics_id_of_topic, $opposition_subtopic->id, true)) {
                $subtopics_id[] = $opposition_subtopic->id;
            }

        }
        return Subtopic::query()->whereIn('id', $subtopics_id)->get();
    }

    public function get_relationship_a_subtopic($topic, $subtopic)
    {
        $subtopicRecord = $topic->subtopics()->firstWhere('id', '=', $subtopic->getRouteKey());

        if (!$subtopicRecord) {
            abort(404);
        }

        return $subtopicRecord;
    }

    public function get_relationship_questions($topic)
    {
        return $topic->questions()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_relationship_a_question($topic, $question)
    {
        $question = $topic->questions()->firstWhere('id', '=', $question->getRouteKey());

        if (!$question) {
            abort(404);
        }

        return $question;
    }

    public function subtopics_get_relationship_questions($topic, $subtopic)
    {
        return $subtopic->questions()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function subtopics_get_relationship_a_question($topic, $subtopic, $question)
    {
        $questionRecord = $subtopic->questions()->firstWhere('id', '=', $question->getRouteKey());

        if (!$questionRecord) {
            abort(404);
        }

        return $questionRecord;
    }

    public function create_relationship_subtopic($request, $topic)
    {
        try {

            DB::beginTransaction();

                $subtopicCreated = Subtopic::query()->create([
                    'name' => $request->get('name'),
                    'topic_id' => $topic->getRouteKey()
                ]);

            DB::commit();

            return (new Subtopic)->applyIncludes()->find($subtopicCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function update_relationship_subtopic($request, $topic, $subtopic)
    {
        try {

            $subtopicRecord = $topic->subtopics()->firstWhere('id', '=', $subtopic->getRouteKey());

            if (!$subtopicRecord) {
                abort(404);
            }

            DB::beginTransaction();

            $subtopic->name = $request->get('name') ?? $subtopic->name;
            $subtopic->save();

            DB::commit();

            return (new Subtopic)->applyIncludes()->find($subtopic->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function delete_relationship_subtopic($topic, $subtopic): void
    {
        try {

            $subtopicRecord = $topic->subtopics()->firstWhere('id', '=', $subtopic->getRouteKey());

            if (!$subtopicRecord) {
                abort(404);
            }

            DB::beginTransaction();

            \Log::debug($subtopic);
            $subtopic->delete();

            DB::commit();

            return;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }
}
