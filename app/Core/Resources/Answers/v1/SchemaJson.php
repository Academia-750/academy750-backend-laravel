<?php
namespace App\Core\Resources\Answers\v1;

use App\Models\Answer;
use App\Core\Resources\Answers\v1\Interfaces\AnswersInterface;
use App\Http\Resources\Api\Answer\v1\AnswerCollection;
use App\Http\Resources\Api\Answer\v1\AnswerResource;
use App\Core\Resources\Answers\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements AnswersInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): AnswerCollection
    {
        return AnswerCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return AnswerResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $answer ): AnswerResource
    {
        return AnswerResource::make(
            $this->eventApp->read( $answer )
        );
    }

    public function update( $request, $answer ): AnswerResource
    {
        return AnswerResource::make(
            $this->eventApp->update( $request, $answer )
        );
    }

    public function delete( $answer ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $answer );
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

}
