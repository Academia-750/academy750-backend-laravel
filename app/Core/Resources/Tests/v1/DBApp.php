<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Tests\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Tests\v1\Services\ActionsTestsRecords;
//use App\Imports\Api\Tests\v1\TestsImport;
use App\Exports\Api\Tests\v1\TestsExport;


class DBApp implements TestsInterface
{
    protected Test $model;

    public function __construct(Test $test ){
        $this->model = $test;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Test{
        try {

            DB::beginTransaction();
                $testCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($testCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $test ): \App\Models\Test{
        return $this->model->applyIncludes()->find($test->getRouteKey());
    }

    public function update( $request, $test ): \App\Models\Test{
        try {

            DB::beginTransaction();
                $test->name = $request->get('name');
                $test->save();
            DB::commit();

            return $this->model->applyIncludes()->find($test->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $test ): void{
        try {

            DB::beginTransaction();
                //$test->delete();
                ActionsTestsRecords::deleteRecord( $test );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('tests'));

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
            $tests = $this->model->query()->whereIn('id', $request->get('tests'))->get();
            $domPDF->loadView('resources.export.templates.pdf.tests', compact('tests'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-tests.pdf');
        }
        return Excel::download(new TestsExport($request->get('tests')), 'tests.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new TestsImport(Auth::user()))->import($request->file('tests'));
    }

}
