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

    public function get_tests_unresolved(){
        return $this->testsInterface->get_tests_unresolved();
    }

    public function fetch_unresolved_test( Test $test ){
        return $this->testsInterface->fetch_unresolved_test( $test );
    }

    public function create_a_quiz ( CreateTestRequest $request ) {
        return $this->testsInterface->create_a_quiz( $request );
    }
}
