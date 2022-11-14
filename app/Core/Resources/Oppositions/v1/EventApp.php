<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventApp implements OppositionsInterface
{
    protected $cacheApp;

    public function __construct(\App\Core\Resources\Oppositions\v1\CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function create( $request ){
        $itemCreatedInstance = $this->cacheApp->create( $request );
        /* broadcast(new CreateOppositionEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $opposition ){
        return $this->cacheApp->read( $opposition );
    }

    public function update( $request, $opposition ){
        /* broadcast(new UpdateOppositionEvent($itemUpdatedInstance)); */
        return $this->cacheApp->update( $request, $opposition );
    }

    public function delete( $opposition ){
        /* broadcast(new DeleteOppositionEvent($opposition)); */

        return $this->cacheApp->delete( $opposition );
    }

    public function mass_selection_for_action( $request ): array{

        /* $records = Opposition::whereIn('id', $request->get('oppositions'));

        broadcast(
            new ActionForMassiveSelectionOppositionEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ){
        $this->cacheApp->import_records( $request );
    }

    public function get_relationship_topics($opposition)
    {
        $this->cacheApp->get_relationship_topics($opposition);
    }

    public function get_relationship_subtopics($opposition)
    {
        $this->cacheApp->get_relationship_subtopics($opposition);
    }
}
