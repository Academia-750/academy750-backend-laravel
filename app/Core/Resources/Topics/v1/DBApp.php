<?php
namespace App\Core\Resources\Topics\v1;

use App\Imports\Api\v1\TopicsImport;
use App\Models\Answer;
use App\Models\Opposition;
use App\Models\Question;
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
        //(new TopicsImport(Auth::user()))->import($request->file('topics'))

        \Log::debug($request->filesTopics);
        \Log::debug($request->all());


        foreach ($request->file('filesTopics') as $file) {
            \Log::debug($file);

            (
            new TopicsImport(Auth::user(), $file->getClientOriginalName())
            )->import($file);
        }

    }

    public function get_relationship_subtopics($topic)
    {
        return $topic->subtopics()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_relationship_oppositions($topic)
    {
        $oppositions_id = $topic->oppositions->pluck("id");

        return (new Opposition)->whereIn("id", $oppositions_id)->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_relationship_a_opposition($topic, $opposition)
    {
        $subtopics_id = [];

        foreach ($opposition->subtopics as $opposition_subtopic) {
            $subtopics_id_of_topic = $topic->subtopics->pluck('id')->toArray();
            if (in_array($opposition_subtopic->id, $subtopics_id_of_topic, true)) {
                $subtopics_id[] = $opposition_subtopic->id;
            }

        }
        return Subtopic::query()->whereIn('id', $subtopics_id)->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
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

    public function get_oppositions_available_of_topic($topic)
    {
        $oppositions_id = $topic->oppositions->pluck('id');

        return (new Opposition)->whereNotIn('id', $oppositions_id->toArray())->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function assign_opposition_with_subtopics_to_topic($request, $topic)
    {
        $opposition = Opposition::query()->findOrFail($request->get('opposition-id'));

        $topics_id_by_opposition = $opposition->topics->pluck("id");

        if (!in_array($topic->getRouteKey(), $topics_id_by_opposition->toArray(), true)) {
            $opposition->topics()->attach($topic->getRouteKey());
        }

        $subtopics_id = $request->get('subtopics');
        $subtopics_id_by_topic = $topic->subtopics->pluck("id");
        $subtopics_id_by_opposition = $opposition->subtopics->pluck("id");

        if (is_array($subtopics_id)) {
            foreach ($subtopics_id as $subtopic_id) {
                if (!in_array($subtopic_id, $subtopics_id_by_topic->toArray(), true)) {
                    abort(404);
                }

                if (!in_array($subtopic_id, $subtopics_id_by_opposition->toArray(), true)) {
                    $opposition->subtopics()->attach($subtopic_id);
                }
            }
        }

        return $this->model->applyIncludes()->find($topic->getRouteKey());

    }

    public function update_subtopics_opposition_by_topic ($request, $topic, $opposition) {

        $topics_id_by_opposition = $opposition->topics->pluck("id");

        if (!in_array($topic->getRouteKey(), $topics_id_by_opposition->toArray(), true)) {
            $opposition->topics()->attach($topic->getRouteKey());
        }

        $subtopics_id_by_opposition = $opposition->subtopics->pluck("id");
        $subtopics_id_by_topic = $topic->subtopics->pluck("id");
        $subtopics_id = $request->get('subtopics');

        if (is_array($subtopics_id)) {
            foreach ($subtopics_id as $subtopic_id) {
                // Validamos que los subtemas enviados pertenezcan al tema actual
                if (!in_array($subtopic_id, $subtopics_id_by_topic->toArray(), true)) {
                    abort(404);
                }

                // Si mandamos un subtema que no está agregado en la oposición, lo agregamos
                if (!in_array($subtopic_id, $subtopics_id_by_opposition->toArray(), true)) {
                    $opposition->subtopics()->attach($subtopic_id);
                }
            }

            $opposition->refresh();

            $subtopics_id_of_this_topic = collect([]);

            foreach ($opposition->subtopics as $opposition_subtopic) {
                $subtopics_id_of_topic = $topic->subtopics->pluck('id')->toArray();
                if (in_array($opposition_subtopic->id, $subtopics_id_of_topic, true)) {
                    $subtopics_id_of_this_topic->push($opposition_subtopic->id);
                }
            }
            // Vamos a comparar directamente aquellos subtemas que mandamos vs los subtemas que tiene la oposicion
            // Aquellos subtemas de la oposicion que no se encuentren en los subtemas que mandamos en la Request, se irán desvinculando los subtemas de su oposicion

            $subtopics_id_for_detach = $subtopics_id_of_this_topic->diff($subtopics_id)->all();


            //foreach ($subtopics_id_for_detach as $subtopic_id) {
            if (is_array($subtopics_id_for_detach) && count($subtopics_id_for_detach) > 0) {
                $opposition->subtopics()->detach($subtopics_id_for_detach);
            }
            //}
        }

        return $this->model->applyIncludes()->find($topic->getRouteKey());
    }

    public function delete_opposition_by_topic($topic, $opposition): void
    {
        // Obtener los subtemas
        $subtopics_id_of_this_topic = collect([]);

        foreach ($opposition->subtopics as $opposition_subtopic) {
            $subtopics_id_of_topic = $topic->subtopics->pluck('id')->toArray();
            if (in_array($opposition_subtopic->id, $subtopics_id_of_topic, true)) {
                $subtopics_id_of_this_topic->push($opposition_subtopic->id);
            }
        }

        $opposition->subtopics()->detach($subtopics_id_of_this_topic->toArray());

        $topics_id_by_opposition = $opposition->topics->pluck("id");

        if (in_array($topic->getRouteKey(), $topics_id_by_opposition->toArray(), true)) {
            $opposition->topics()->detach($topic->getRouteKey());
        }

    }

    public function topic_get_relationship_questions($topic)
    {
        return $topic->questions()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function topic_get_a_question($topic, $question)
    {
        return (new Question)->applyIncludes()->find($question->getRouteKey());
    }

    public function topic_create_a_question($request, $topic)
    {
        try {

            DB::beginTransaction();
                $questionCreated = $topic->questions()->create([
                    'question' => $request->get('question-text'),
                    'reason' => $request->get('reason'),
                    'is_visible' => $request->get('is_visible')
                ]);

                foreach ( $request->get('answers') as $answer) {
                    Answer::query()->create([
                        "answer" => $answer["answer-text"],
                        "is_grouper_answer" => $answer["is_grouper_answer"],
                        "is_correct_answer" => $answer["is_correct_answer"],
                        "question_id" => $questionCreated->id,
                    ]);
                }
            DB::commit();

            return $this->model->applyIncludes()->find($topic->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function topic_update_a_question($request, $topic, $question)
    {
        try {
            DB::beginTransaction();

            $question->update([
                'question' => $request->get('question-text') ?? $question->question,
                'reason' => $request->get('reason') ?? $question->reason,
                'is_visible' => $request->get('is_visible') ?? $question->is_visible
            ]);

            foreach ( $request->get('answers') as $answer) {
                $answer = Answer::query()->findOrFail($answer["id"]);

                $answer->answer = $answer["answer-text"];
                $answer->is_grouper_answer = $answer["is_grouper_answer"];
                $answer->is_correct_answer = $answer["is_correct_answer"];
                $answer->save();

            }

            DB::commit();

            return $this->model->applyIncludes()->find($topic->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function topic_delete_a_question($topic, $question)
    {
        // TODO: Implement topic_delete_a_question() method.
    }
}
