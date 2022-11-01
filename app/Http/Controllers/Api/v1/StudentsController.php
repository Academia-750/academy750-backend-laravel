<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Student;
use App\Core\Resources\Students\v1\Interfaces\StudentsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Students\CreateStudentRequest;
use App\Http\Requests\Api\v1\Students\UpdateStudentRequest;
use App\Http\Requests\Api\v1\Students\ActionForMassiveSelectionStudentsRequest;
use App\Http\Requests\Api\v1\Students\ExportStudentsRequest;
use App\Http\Requests\Api\v1\Students\ImportStudentsRequest;

class StudentsController extends Controller
{
    protected StudentsInterface $studentsInterface;

    public function __construct(StudentsInterface $studentsInterface ){
        $this->studentsInterface = $studentsInterface;
    }

    public function index(){
        return $this->studentsInterface->index();
    }

    public function create(CreateStudentRequest $request){
        return $this->studentsInterface->create($request);
    }

    public function read(Student $student){
        return $this->studentsInterface->read( $student );
    }

    public function update(UpdateStudentRequest $request, Student $student){
        return $this->studentsInterface->update( $request, $student );
    }

    public function delete(Student $student){
        return $this->studentsInterface->delete( $student );
    }

    public function mass_selection_for_action(ActionForMassiveSelectionStudentsRequest $request): string{
        return $this->studentsInterface->mass_selection_for_action( $request );
    }

    public function export_records(ExportStudentsRequest $request){
        return $this->studentsInterface->export_records( $request );
    }

    public function import_records(ImportStudentsRequest $request){
        return $this->studentsInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/student.csv', 'template_import_student');
    }
}
