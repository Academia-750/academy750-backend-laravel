<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Tests\CreateTestRequest;
use App\Http\Requests\Api\v1\Tests\UpdateTestRequest;
use App\Http\Requests\Api\v1\Tests\ActionForMassiveSelectionTestsRequest;
use App\Http\Requests\Api\v1\Tests\ExportTestsRequest;
use App\Http\Requests\Api\v1\Tests\ImportTestsRequest;

class TestsController extends Controller
{
    protected TestsInterface $testsInterface;

    public function __construct(TestsInterface $testsInterface ){
        $this->testsInterface = $testsInterface;
    }

    public function index(){
        return $this->testsInterface->index();
    }

    public function create(CreateTestRequest $request){
        return $this->testsInterface->create($request);
    }

    public function read(Test $test){
        return $this->testsInterface->read( $test );
    }

    public function update(UpdateTestRequest $request, Test $test){
        return $this->testsInterface->update( $request, $test );
    }

    public function delete(Test $test){
        return $this->testsInterface->delete( $test );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionTestsRequest $request): string{
        return $this->testsInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportTestsRequest $request){
        return $this->testsInterface->export_records( $request );
    }

    public function import_records(ImportTestsRequest $request){
        return $this->testsInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/test.csv', 'template_import_test');
    }
}
