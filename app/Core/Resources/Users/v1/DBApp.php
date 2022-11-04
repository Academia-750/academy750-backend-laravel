<?php
namespace App\Core\Resources\Users\v1;

use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Exports\Api\Users\v1\UsersExport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\Api\Users\v1\UserImport;



class DBApp implements UsersInterface
{
    protected User $model;

    public function __construct(User $student ){
        $this->model = $student;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Student{
        try {

            DB::beginTransaction();
                $studentCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($studentCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $student ): \App\Models\Student{
        return $this->model->applyIncludes()->find($student->getRouteKey());
    }

    public function update( $request, $student ): \App\Models\Student{
        try {

            DB::beginTransaction();
                $student->name = $request->get('name');
                $student->save();
            DB::commit();

            return $this->model->applyIncludes()->find($student->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $student ): void{
        try {

            DB::beginTransaction();
                $student->delete();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function mass_selection_for_action( $request ): string{
        try {

            DB::beginTransaction();
                $message = null;
                if( $request->get('action') === 'delete' ){
                    foreach ($request->get('students') as $student){
                        $studentDelete = $this->model->firstWhere('id',$student);
                        $studentDelete->delete();
                    }
                    $process = true;
                    $message = "Los registros seleccionados han sido eliminados.";
                }
            DB::commit();

            if ($process) {
                return $message;
            }else {
                return "No se ha realizado ninguna acción";
            }

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function export_records( $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse{
        if ($request->get('type') === 'pdf') {
            $domPDF = App::make('dompdf.wrapper');
            $students = $this->model->query()->whereIn('id', $request->get('students'))->get();
            $domPDF->loadView('resources.export.templates.pdf.students', compact('students'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-students.pdf');
        }
        return Excel::download(new UsersExport($request->get('students')), 'students.'. $request->get('type'));
    }

    public function import_records( $request ): string{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new UserImport(Auth::user()))->import($request->file('students'));

         /*
         // Lanzamiento de errores en caso de validacion sin uso de Queues
         if ($importInstance->failures()->isNotEmpty()) {
             throw ValidationException::withMessages([
                 'errors' => [
                     $importInstance->failures()
                 ]
             ]);
         }*/
        return "Proceso de importación iniciado";
    }

}
