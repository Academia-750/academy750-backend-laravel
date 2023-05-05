<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;

class CacheApp implements OppositionsInterface
{
    protected $dbApp;

    public function __construct(\App\Core\Resources\Oppositions\v1\DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index(){
        return $this->dbApp->index();
    }

    public function create( $request ){
        return $this->dbApp->create( $request );
    }

    public function read( $opposition ){
        return $this->dbApp->read( $opposition );
    }

    public function update( $request, $opposition ){
        return $this->dbApp->update( $request, $opposition );
    }

    public function delete( $opposition ){
        return $this->dbApp->delete( $opposition );
    }

    public function mass_selection_for_action( $request ): array{
        return $this->dbApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->dbApp->export_records( $request );
    }

    public function get_relationship_syllabus($opposition)
    {
        return $this->dbApp->get_relationship_syllabus( $opposition );
    }
}
