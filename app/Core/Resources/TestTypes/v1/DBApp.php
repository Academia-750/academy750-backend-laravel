<?php
namespace App\Core\Resources\TestTypes\v1;

use App\Models\TestType;
use App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\TestTypes\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\TestTypes\v1\Services\ActionsTestTypesRecords;
//use App\Imports\Api\TestTypes\v1\TestTypesImport;
use App\Exports\Api\TestTypes\v1\TestTypesExport;


class DBApp implements TestTypesInterface
{
    protected TestType $model;

    public function __construct(TestType $test_type ){
        $this->model = $test_type;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\TestType{
        try {

            DB::beginTransaction();
                $test_typeCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($test_typeCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $test_type ): \App\Models\TestType{
        return $this->model->applyIncludes()->find($test_type->getRouteKey());
    }

    public function update( $request, $test_type ): \App\Models\TestType{
        try {

            DB::beginTransaction();
                $test_type->name = $request->get('name');
                $test_type->save();
            DB::commit();

            return $this->model->applyIncludes()->find($test_type->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $test_type ): void{
        try {

            DB::beginTransaction();
                //$test_type->delete();
                ActionsTestTypesRecords::deleteRecord( $test_type );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('test_types'));

            DB::commit();

            if (count($information) === 0) {
                $information[] = "No hay registros afectados";
            }

            return $information;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function export_records( $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse{
        if ($request->get('type') === 'pdf') {
            $domPDF = App::make('dompdf.wrapper');
            $test_types = $this->model->query()->whereIn('id', $request->get('test-types'))->get();
            $domPDF->loadView('resources.export.templates.pdf.test-types', compact('test_types'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-test-types.pdf');
        }
        return Excel::download(new TestTypesExport($request->get('test-types')), 'test-types.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new TestTypesImport(Auth::user()))->import($request->file('test-types'));
    }

}
