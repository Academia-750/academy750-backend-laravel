<?php
namespace App\Core\Resources\Topics\v1;

use App\Models\Topic;
use App\Core\Resources\Topics\v1\Interfaces\TopicsInterface;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use App\Http\Resources\Api\Topic\v1\TopicResource;
use App\Core\Resources\Topics\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TopicsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): TopicCollection
    {
        return TopicCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return TopicResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $topic ): TopicResource
    {
        return TopicResource::make(
            $this->eventApp->read( $topic )
        );
    }

    public function update( $request, $topic ): TopicResource
    {
        return TopicResource::make(
            $this->eventApp->update( $request, $topic )
        );
    }

    public function delete( $topic ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $topic );
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
