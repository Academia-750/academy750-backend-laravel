<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\v1\Questionnaires\ResolveQuestionOfTestRequest;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Questionnaires\CreateTestRequest;

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

    public function fetch_card_memory( Test $test ){
        return $this->testsInterface->fetch_card_memory( $test );
    }

    public function fetch_test_completed( Test $test ){
        return $this->testsInterface->fetch_test_completed( $test );
    }

    public function create_a_quiz ( CreateTestRequest $request ) {
        return $this->testsInterface->create_a_quiz( $request );
    }

    public function resolve_a_question_of_test ( ResolveQuestionOfTestRequest $request ) {
        return $this->testsInterface->resolve_a_question_of_test( $request );
    }

    public function get_cards_memory () {
        return $this->testsInterface->get_cards_memory();
    }

    public function grade_a_test (Request $request, Test $test) {
        return $this->testsInterface->grade_a_test($request, $test);
    }
}
