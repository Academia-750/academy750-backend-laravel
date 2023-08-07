<?php
namespace App\Core\Resources\Topics\v1;

use App\Core\Resources\Topics\v1\Services\GetTopicsAvailableForTestService;
use App\Imports\Api\v1\SubtopicsImport;
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
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Topics\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Topics\v1\Services\ActionsTopicsRecords;


class DBApp implements TopicsInterface
{
    protected Topic $model;

    public function __construct(Topic $topic)
    {
        $this->model = $topic;
    }

    public function index()
    {
        return $this->model::applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_topics_available_for_create_test($request)
    {
        try {
            // \Log::debug("---------------------Data Request---------------------");
            // \Log::debug($request);
            //// \Log::debug(implode(',', $request->get('topics-group-id')));


            $topics_procedure_results = [];

            /*foreach ($request->get('topics-group-id') as $topicGroupId) {
                // \Log::debug("---------------------Iteración {$topicGroupId}---------------------");
                // \Log::debug($topicGroupId);

                $topics_procedure_results = GetTopicsAvailableForTestService::executeQueryFilterTopicsAvailableByOppositionAndTopicGroup($request->get('opposition-id'), $topicGroupId);
                // \Log::debug($topics_procedure_results);

                $topics_id = array_merge(
                    $topics_id,
                    $topics_procedure_results
                );


            }*/

            $topics_id = GetTopicsAvailableForTestService::executeQueryFilterTopicsAvailableByOppositionAndTopicGroup(
                $request->get('opposition-id'),
                implode(',', (array) $request->get('topics-group-id'))
            );


            // \Log::debug("---------------------Final Array Topics ID---------------------");
            // \Log::debug($topics_id);

            //$topics_id = collect($topics_id)->pluck('id')->toArray();
            //// \Log::debug($topics_id);

            return $this->model->query()->whereIn('id', $topics_id)->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
            //return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function create($request): \App\Models\Topic
    {

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
            abort(500, $e->getMessage());
        }
    }

    public function read($topic): \App\Models\Topic
    {
        return $this->model->applyIncludes()->find($topic->getRouteKey());
    }

    public function update($request, $topic): \App\Models\Topic
    {
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

    public function delete($topic): void
    {
        try {

            DB::beginTransaction();
            //$topic->delete();
            ActionsTopicsRecords::deleteRecord($topic);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records($request): array
    {
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

    public function export_records($request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if ($request->get('type') === 'pdf') {
            $domPDF = App::make('dompdf.wrapper');
            $topics = $this->model->query()->whereIn('id', $request->get('topics'))->get();
            $domPDF->loadView('resources.export.templates.pdf.topics', compact('topics'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-topics.pdf');
        }
        return Excel::download(new TopicsExport($request->get('topics')), 'topics.' . $request->get('type'));
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

    public function get_relationship_subtopics_by_opposition($topic, $opposition)
    {
        $subtopics_id = [];

        foreach ($opposition->subtopics as $opposition_subtopic) {
            $subtopics_id_of_topic = $topic->subtopics->pluck('id')->toArray();
            if (in_array($opposition_subtopic->id, $subtopics_id_of_topic, true)) {
                $subtopics_id[] = $opposition_subtopic->id;
            }

        }
        return Subtopic::query()->whereIn('id', $subtopics_id)->where('is_available', 'yes')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
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
        return $topic->questions()->applyFilters()->where('is_visible', 'yes')->applySorts()->applyIncludes()->jsonPaginate();
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
        return $subtopic->questions()->applyFilters()->where('is_visible', 'yes')->applySorts()->applyIncludes()->jsonPaginate();
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

            $countTestsOfThisSubtopic = $subtopicRecord->tests()->count();

            if ($countTestsOfThisSubtopic > 0) {
                $subtopicRecord->is_available = 'no';
                $subtopicRecord->save();

                $subtopicRecord->questions->each(function ($question) {
                    $question->update(['is_visible' => 'no']);
                });
            } else {
                DB::table('oppositionables')
                    ->where(function ($query) use ($subtopicRecord) {
                        $query->where(function ($query) use ($subtopicRecord) {
                            $query->where('oppositionable_type', Subtopic::class)
                                ->where('oppositionable_id', $subtopicRecord->id);
                        });
                    })
                    ->delete();

                $subtopic->questions()->delete();

                $subtopic->delete();
            }

            ActionsTopicsRecords::deleteQuestionsUsedInTestsByTopic($subtopic->id, "subtopic_id");

            return;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function get_oppositions_available_of_topic($topic)
    {
        $oppositions_id = $topic->oppositions()->pluck('oppositions.id');

        return (new Opposition)->whereNotIn('id', $oppositions_id->toArray())->where('is_available', 'yes')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
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

    public function update_subtopics_opposition_by_topic($request, $topic, $opposition)
    {

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


        return $topic->questions()->applyFilters()->where('is_visible', 'yes')->applySorts()->applyIncludes()->jsonPaginate();
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

            foreach ($request->get('answers') as $answer) {
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
            abort(500, $e->getMessage());
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

            foreach ($request->get('answers') as $answer) {
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
            abort(500, $e->getMessage());
        }
    }

    public function topic_delete_a_question($topic, $question)
    {
        // TODO: Implement topic_delete_a_question() method.
    }

    public function topic_relationship_questions()
    {
        return Topic::applyFilters()->where('is_visible', 'yes')->applySorts()->applyIncludes()->get();
    }

    public function import_records($request): void
    {
        // IMPORTAR TEMAS

        try {

            $filesTopics = $request->file('filesTopics') ?? [];

            foreach ($filesTopics as $file) {

                /*dispatch(
                    new ImportTopicsJob( $file, Auth::user() )
                );*/

                (
                    new TopicsImport(Auth::user(), $file->getClientOriginalName())
                )->import($file);

            }

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

    }

    public function import_subtopics_by_topics($request)
    {

        try {

            $filesSubtopics = $request->file('filesSubtopics') ?? [];

            foreach ($filesSubtopics as $file) {

                /*dispatch(
                    new ImportSubtopicsJob( $file, Auth::user() )
                );*/

                (
                    new SubtopicsImport(Auth::user(), $file->getClientOriginalName())
                )->import($file);

            }

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function topics_get_worst_topics_of_student()
    {
        $topics_data = DB::select(
            "call get_5_worse_topic_results_by_user_procedure(?)",
            array(Auth::user()?->getRouteKey())
        ); //search_question_in_topics_and_subtopics

        // \Log::debug($topics_data);

        $topics_data_mapped = array_map(static function ($topic) {
            $itemCasted = (array) $topic;
            return [
                'topic_id' => $itemCasted['TOPIC_ID'],
                'topic_name' => $itemCasted['TOPIC_NAME'],
                'total_questions_correct' => $itemCasted['CORRECT_ANS'],
                'total_questions_wrong' => $itemCasted['INCORRECT_ANS'],
                'total_questions_unanswered' => $itemCasted['UNANSWERED_ANS'],
                'percentage_without_format' => $itemCasted['PERCENTAGE'],
                'percentage_with_format' => ((string) ((int) $itemCasted['PERCENTAGE'])) . '%',
            ];
        }, (array) $topics_data);

        // \Log::debug($topics_data_mapped);

        return $topics_data_mapped;
        /*$topics_id = collect($topics_id)->pluck('id')->toArray();

        return $this->model->query()->whereIn('id', $topics_id)->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();*/
    }
}