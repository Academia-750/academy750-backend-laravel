<?php
namespace App\Core\Resources\Students\v1;

use App\Models\Student;
use App\Core\Resources\Students\v1\Interfaces\StudentsInterface;
use App\Http\Resources\Api\Student\v1\StudentCollection;
use App\Http\Resources\Api\Student\v1\StudentResource;
use Illuminate\Support\Str;

class SchemaJson implements StudentsInterface
{
    protected $eventApp;

    public function __construct(\App\Core\Resources\Students\v1\EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(){
        return StudentCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ){
        return StudentResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $student ){
        return StudentResource::make(
            $this->eventApp->read( $student )
        );
    }

    public function update( $request, $student ){
        return StudentResource::make(
            $this->eventApp->update( $request, $student )
        );
    }

    public function delete( $student ){
        return response()->json($this->eventApp->delete( $student ), 204);
    }

    public function mass_selection_for_action( $request ): string{
        return response()->json([
            'message' => $this->eventApp->mass_selection_for_action( $request )
        ], 200);
    }

    public function export_records( $request ){
        return $this->eventApp->export_records( $request );
    }

    public function import_records( $request ){
        return $this->eventApp->import_records( $request );
    }

}
