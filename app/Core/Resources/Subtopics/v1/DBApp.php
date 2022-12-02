<?php
namespace App\Core\Resources\Subtopics\v1;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Subtopic;
use App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Subtopics\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Subtopics\v1\Services\ActionsSubtopicsRecords;
//use App\Imports\Api\Subtopics\v1\SubtopicsImport;
use App\Exports\Api\Subtopics\v1\SubtopicsExport;


class DBApp implements SubtopicsInterface
{
    protected Subtopic $model;

    public function __construct(Subtopic $subtopic ){
        $this->model = $subtopic;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Subtopic{
        try {

            DB::beginTransaction();
                $subtopicCreated = $this->model->query()->create([
                    'name' => $request->get('name'),
                    'is_available' => 'yes'
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($subtopicCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $subtopic ): \App\Models\Subtopic{
        return $this->model->applyIncludes()->find($subtopic->getRouteKey());
    }

    public function update( $request, $subtopic ): \App\Models\Subtopic{
        try {

            DB::beginTransaction();
                $subtopic->name = $request->get('name') ?? $subtopic->name;
                $subtopic->save();
            DB::commit();

            return $this->model->applyIncludes()->find($subtopic->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $subtopic ): void{
        try {

            DB::beginTransaction();
                //$subtopic->delete();
                ActionsSubtopicsRecords::deleteRecord( $subtopic );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('subtopics'));

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
            $subtopics = $this->model->query()->whereIn('id', $request->get('subtopics'))->get();
            $domPDF->loadView('resources.export.templates.pdf.subtopics', compact('subtopics'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-subtopics.pdf');
        }
        return Excel::download(new SubtopicsExport($request->get('subtopics')), 'subtopics.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new SubtopicsImport(Auth::user()))->import($request->file('subtopics'));
    }

    public function subtopic_get_relationship_questions($subtopic)
    {
        return $subtopic->questions()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function subtopic_get_a_question($subtopic, $question)
    {
        return Question::query()->applyIncludes()->find($question->getRouteKey());
    }

    public function subtopic_create_a_question($request, $subtopic)
    {
        try {
            DB::beginTransaction();
            $questionCreated = $subtopic->questions()->create([
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

            return $this->model->applyIncludes()->find($subtopic->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function subtopic_update_a_question($request, $subtopic, $question)
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

            return $this->model->applyIncludes()->find($subtopic->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function subtopic_delete_a_question($subtopic, $question)
    {
        try {
            DB::beginTransaction();

            /*$questionCreated = $subtopic->questions()->create([
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
            }*/

                $question->delete();
            DB::commit();

            return $this->model->applyIncludes()->find($subtopic->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }
}
