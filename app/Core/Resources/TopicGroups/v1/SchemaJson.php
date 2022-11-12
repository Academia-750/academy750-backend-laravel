<?php
namespace App\Core\Resources\TopicGroups\v1;

use App\Models\TopicGroup;
use App\Core\Resources\TopicGroups\v1\Interfaces\TopicGroupsInterface;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupCollection;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupResource;
use App\Core\Resources\TopicGroups\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TopicGroupsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): TopicGroupCollection
    {
        return TopicGroupCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return TopicGroupResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $topic_group ): TopicGroupResource
    {
        return TopicGroupResource::make(
            $this->eventApp->read( $topic_group )
        );
    }

    public function update( $request, $topic_group ): TopicGroupResource
    {
        return TopicGroupResource::make(
            $this->eventApp->update( $request, $topic_group )
        );
    }

    public function delete( $topic_group ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $topic_group );
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
