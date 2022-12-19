<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Questionnaires\CreateTestRequest;
use App\Http\Requests\Api\v1\Questionnaires\UpdateTestRequest;
use App\Http\Requests\Api\v1\Questionnaires\ActionForMassiveSelectionTestsRequest;
use App\Http\Requests\Api\v1\Questionnaires\ExportTestsRequest;
use App\Http\Requests\Api\v1\Questionnaires\ImportTestsRequest;

class QuestionnairesController extends Controller
{
    protected TestsInterface $testsInterface;

    public function __construct(TestsInterface $testsInterface ){
        $this->testsInterface = $testsInterface;
    }

    public function index(){
        return $this->testsInterface->index();
    }

    public function read( Test $test ){
        return $this->testsInterface->read( $test );
    }

    public function generate ( CreateTestRequest $request ) {
        return $this->testsInterface->generate( $request );
    }
}
