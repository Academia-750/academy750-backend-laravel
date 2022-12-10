<?php
namespace App\Core\Resources\Questions\v1;

use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Question\v1\QuestionResource;
use App\Core\Resources\Questions\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements QuestionsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): QuestionCollection
    {
        return QuestionCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return QuestionResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $question ): QuestionResource
    {
        return QuestionResource::make(
            $this->eventApp->read( $question )
        );
    }

    public function update( $request, $question ): QuestionResource
    {
        return QuestionResource::make(
            $this->eventApp->update( $request, $question )
        );
    }

    public function delete( $question ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $question );
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

    public function generate(): QuestionCollection
    {
        return QuestionCollection::make(
            $this->eventApp->generate()
        );
    }
}
