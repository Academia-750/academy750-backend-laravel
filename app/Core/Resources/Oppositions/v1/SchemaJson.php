<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Http\Resources\Api\Subtopic\v1\SubtopicCollection;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use App\Http\Resources\Api\Opposition\v1\OppositionCollection;
use App\Http\Resources\Api\Opposition\v1\OppositionResource;
use Illuminate\Support\Str;

class SchemaJson implements OppositionsInterface
{
    protected $eventApp;

    public function __construct(\App\Core\Resources\Oppositions\v1\EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(){
        return OppositionCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ){
        return OppositionResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $opposition ){
        return OppositionResource::make(
            $this->eventApp->read( $opposition )
        );
    }

    public function update( $request, $opposition ){
        return OppositionResource::make(
            $this->eventApp->update( $request, $opposition )
        );
    }

    public function delete( $opposition ){
        return response()->json($this->eventApp->delete( $opposition ), 204);
    }

    public function mass_selection_for_action( $request ): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'information' => $this->eventApp->mass_selection_for_action( $request )
        ]);
    }

    public function export_records( $request ){
        return $this->eventApp->export_records( $request );
    }

    public function import_records( $request ){
        return $this->eventApp->import_records( $request );
    }

    public function get_relationship_topics($opposition): TopicCollection
    {
        return TopicCollection::make(
            $this->eventApp->get_relationship_topics($opposition)
        );
    }

    public function get_relationship_subtopics($opposition): SubtopicCollection
    {
        return SubtopicCollection::make(
            $this->eventApp->get_relationship_subtopics($opposition)
        );
    }

}
