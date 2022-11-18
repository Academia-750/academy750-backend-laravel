<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class Authorizer implements OppositionsInterface
{
    protected $schemaJson;

    public function __construct(\App\Core\Resources\Oppositions\v1\SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(){
        Gate::authorize('index', Opposition::class );
        return $this->schemaJson->index();
    }

    public function create( $request ){
        Gate::authorize('create', Opposition::class );
        return $this->schemaJson->create($request);
    }

    public function read( $opposition ){
        Gate::authorize('read', $opposition );
        return $this->schemaJson->read( $opposition );
    }

    public function update( $request, $opposition ){
        Gate::authorize('update', $opposition );
        return $this->schemaJson->update( $request, $opposition );
    }

    public function delete( $opposition ){
        Gate::authorize('delete', $opposition );
        return $this->schemaJson->delete( $opposition );
    }

    public function mass_selection_for_action( $request ){
        Gate::authorize('mass_selection_for_action', Opposition::class );
        return $this->schemaJson->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        Gate::authorize('export_records', Opposition::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ){
        Gate::authorize('import_records', Opposition::class );
        return $this->schemaJson->import_records( $request );
    }

    public function get_relationship_syllabus($opposition)
    {
        return $this->schemaJson->get_relationship_syllabus($opposition);
    }
}
