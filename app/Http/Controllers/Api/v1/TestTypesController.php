<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\TestType;
use App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\TestTypes\CreateTestTypeRequest;
use App\Http\Requests\Api\v1\TestTypes\UpdateTestTypeRequest;
use App\Http\Requests\Api\v1\TestTypes\ActionForMassiveSelectionTestTypesRequest;
use App\Http\Requests\Api\v1\TestTypes\ExportTestTypesRequest;
use App\Http\Requests\Api\v1\TestTypes\ImportTestTypesRequest;

class TestTypesController extends Controller
{
    protected TestTypesInterface $testTypesInterface;

    public function __construct(TestTypesInterface $testTypesInterface ){
        $this->testTypesInterface = $testTypesInterface;
    }

    public function index(){
        return $this->testTypesInterface->index();
    }

    public function create(CreateTestTypeRequest $request){
        return $this->testTypesInterface->create($request);
    }

    public function read(TestType $test_type){
        return $this->testTypesInterface->read( $test_type );
    }

    public function update(UpdateTestTypeRequest $request, TestType $test_type){
        return $this->testTypesInterface->update( $request, $test_type );
    }

    public function delete(TestType $test_type){
        return $this->testTypesInterface->delete( $test_type );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionTestTypesRequest $request): string{
        return $this->testTypesInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportTestTypesRequest $request){
        return $this->testTypesInterface->export_records( $request );
    }

    public function import_records(ImportTestTypesRequest $request){
        return $this->testTypesInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/test_type.csv', 'template_import_test_type');
    }
}
