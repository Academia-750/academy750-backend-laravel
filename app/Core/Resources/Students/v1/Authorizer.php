<?php
namespace App\Core\Resources\Students\v1;

use App\Models\Student;
use App\Core\Resources\Students\v1\Interfaces\StudentsInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class Authorizer implements StudentsInterface
{
    protected $schemaJson;

    public function __construct(\App\Core\Resources\Students\v1\SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(){
        Gate::authorize('index', Student::class );
        return $this->schemaJson->index();
    }

    public function create( $request ){
        Gate::authorize('create', Student::class );
        return $this->schemaJson->create($request);
    }

    public function read( $student ){
        Gate::authorize('read', $student );
        return $this->schemaJson->read( $student );
    }

    public function update( $request, $student ){
        Gate::authorize('update', $student );
        return $this->schemaJson->update( $request, $student );
    }

    public function delete( $student ){
        Gate::authorize('delete', $student );
        return $this->schemaJson->delete( $student );
    }

    public function mass_selection_for_action( $request ): string{
        Gate::authorize('mass_selection_for_action', Student::class );
        return $this->schemaJson->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        Gate::authorize('export_records', Student::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ){
        Gate::authorize('import_records', Student::class );
        return $this->schemaJson->import_records( $request );
    }

}
