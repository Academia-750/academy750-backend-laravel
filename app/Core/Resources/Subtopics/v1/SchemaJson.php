<?php
namespace App\Core\Resources\Subtopics\v1;

use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Question\v1\QuestionResource;
use App\Models\Subtopic;
use App\Core\Resources\Subtopics\v1\Interfaces\SubtopicsInterface;
use App\Http\Resources\Api\Subtopic\v1\SubtopicCollection;
use App\Http\Resources\Api\Subtopic\v1\SubtopicResource;
use App\Core\Resources\Subtopics\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements SubtopicsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): SubtopicCollection
    {
        return SubtopicCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return SubtopicResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $subtopic ): SubtopicResource
    {
        return SubtopicResource::make(
            $this->eventApp->read( $subtopic )
        );
    }

    public function update( $request, $subtopic ): SubtopicResource
    {
        return SubtopicResource::make(
            $this->eventApp->update( $request, $subtopic )
        );
    }

    public function delete( $subtopic ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $subtopic );
        return response()->noContent();
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'information' => $this->eventApp->action_for_multiple_records( $request )
        ], 200);
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->export_records( $request );

        return response()->json([
            'message' => "Proceso de exportación iniciada"
        ], 200);
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->import_records( $request );

        return response()->json([
            'message' => "Proceso de importación iniciada"
        ], 200);
    }

    public function subtopic_get_relationship_questions($subtopic)
    {
        return QuestionCollection::make(
            $this->eventApp->subtopic_get_relationship_questions($subtopic)
        );
    }

    public function subtopic_get_a_question($subtopic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_get_a_question($subtopic, $question)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }

    public function subtopic_create_a_question($request, $subtopic)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_create_a_question($request, $subtopic)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }

    public function subtopic_update_a_question($request, $subtopic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_update_a_question($request, $subtopic, $question)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }

    public function subtopic_delete_a_question($subtopic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_delete_a_question($subtopic, $question)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }
}
