<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;


class EventApp implements OppositionsInterface
{
    protected $cacheApp;

    public function __construct(\App\Core\Resources\Oppositions\v1\CacheApp $cacheApp)
    {
        $this->cacheApp = $cacheApp;
    }

    public function index($request)
    {
        return $this->cacheApp->index($request);
    }

    public function create($request)
    {
        $itemCreatedInstance = $this->cacheApp->create($request);
        /* broadcast(new CreateOppositionEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read($opposition)
    {
        return $this->cacheApp->read($opposition);
    }

    public function update($request, $opposition)
    {
        /* broadcast(new UpdateOppositionEvent($itemUpdatedInstance)); */
        return $this->cacheApp->update($request, $opposition);
    }

    public function delete($opposition)
    {
        /* broadcast(new DeleteOppositionEvent($opposition)); */

        return $this->cacheApp->delete($opposition);
    }

    public function mass_selection_for_action($request): array
    {
        return $this->cacheApp->mass_selection_for_action($request);
    }

    public function get_relationship_syllabus($opposition)
    {
        return $this->cacheApp->get_relationship_syllabus($opposition);
    }

    public function get_relationship_subtopics($topic, $opposition)
    {
        return $this->cacheApp->get_relationship_subtopics($topic, $opposition);
    }
}