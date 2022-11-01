<?php
namespace App\Core\Resources\Students\v1;

use App\Models\Student;
use App\Core\Resources\Students\v1\Interfaces\StudentsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventApp implements StudentsInterface
{
    protected $cacheApp;

    public function __construct(\App\Core\Resources\Students\v1\CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function create( $request ){
        $itemCreatedInstance = $this->cacheApp->create( $request );
        /* broadcast(new CreateStudentEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $student ){
        return $this->cacheApp->read( $student );
    }

    public function update( $request, $student ){
        $itemUpdatedInstance = $this->cacheApp->update( $request );
        /* broadcast(new UpdateStudentEvent($itemUpdatedInstance)); */
        return $this->cacheApp->update( $request, $student );
    }

    public function delete( $student ){
        /* broadcast(new DeleteStudentEvent($student)); */

        return $this->cacheApp->delete( $student );
    }

    public function mass_selection_for_action( $request ): string{

        /* $records = Student::whereIn('id', $request->get('students'));

        broadcast(
            new ActionForMassiveSelectionStudentEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ){
        $this->cacheApp->import_records( $request );
    }

}
