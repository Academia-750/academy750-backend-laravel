<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use App\Http\Resources\Api\Opposition\v1\OppositionCollection;
use App\Http\Resources\Api\Opposition\v1\OppositionResource;


class SchemaJson implements OppositionsInterface
{
    protected $eventApp;

    public function __construct(\App\Core\Resources\Oppositions\v1\EventApp $eventApp)
    {
        $this->eventApp = $eventApp;
    }

    public function index($request)
    {
        return OppositionCollection::make(
            $this->eventApp->index($request)
        );
    }

    public function create($request)
    {
        return OppositionResource::make($this->eventApp->create($request))
            ->response()
            ->setStatusCode(201);
    }

    public function read($opposition)
    {
        return OppositionResource::make(
            $this->eventApp->read($opposition)
        );
    }

    public function update($request, $opposition)
    {
        return OppositionResource::make(
            $this->eventApp->update($request, $opposition)
        );
    }

    public function delete($opposition)
    {
        return response()->json($this->eventApp->delete($opposition), 204);
    }

    public function mass_selection_for_action($request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'information' => $this->eventApp->mass_selection_for_action($request)
        ]);
    }

    public function get_relationship_syllabus($opposition): \Illuminate\Http\JsonResponse
    {

        return response()->json(
            $this->eventApp->get_relationship_syllabus($opposition)
        );
    }

}