<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\v1\Users\ContactUsPageRequest;
use App\Http\Requests\Api\v1\Users\FetchHistoryQuestionsByTypeAndPeriodOfStudentRequest;
use App\Http\Requests\Api\v1\Users\FetchHistoryStatisticalDataGraphByStudentRequest;
use App\Models\Topic;
use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Users\CreateUserRequest;
use App\Http\Requests\Api\v1\Users\UpdateUserRequest;
use App\Http\Requests\Api\v1\Users\ActionForMassiveSelectionUsersRequest;
use App\Http\Requests\Api\v1\Users\ExportUsersRequest;
use App\Http\Requests\Api\v1\Users\ImportUsersRequest;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    protected UsersInterface $usersInterface;

    public function __construct(UsersInterface $usersInterface ){
        $this->usersInterface = $usersInterface;
    }

    public function index(){
        return $this->usersInterface->index();
    }

    public function create(CreateUserRequest $request){
        return $this->usersInterface->create($request);
    }

    public function read(User $user){
        return $this->usersInterface->read( $user );
    }

    public function update(UpdateUserRequest $request, User $user){
        return $this->usersInterface->update( $request, $user );
    }

    public function delete(User $user){
        return $this->usersInterface->delete( $user );
    }

    public function mass_selection_for_action(ActionForMassiveSelectionUsersRequest $request): string{
        return $this->usersInterface->mass_selection_for_action( $request );
    }

    public function disable_account(Request $request, User $user){
        return $this->usersInterface->disable_account( $request, $user );
    }

    public function enable_account(Request $request, User $user){
        return $this->usersInterface->enable_account( $request, $user );
    }

    public function contactsUS(ContactUsPageRequest $request){
        return $this->usersInterface->contactsUS( $request );
    }

    public function fetch_history_statistical_data_graph_by_student(FetchHistoryStatisticalDataGraphByStudentRequest $request){

        return $this->usersInterface->get_history_statistical_data_graph_by_student($request);
    }

    public function fetch_history_questions_by_type_and_period(){

        return $this->usersInterface->fetch_history_questions_by_type_and_period();
    }

    public function fetch_history_questions_wrong_by_topic_of_student(Topic $topic){

        return $this->usersInterface->fetch_history_questions_wrong_by_topic_of_student($topic);
    }

    public function fetch_history_tests_completed_by_student(){

        return $this->usersInterface->fetch_history_tests_completed_by_student();
    }

    public function fetch_topics_available_in_tests(){

        return $this->usersInterface->fetch_topics_available_in_tests();
    }

    public function fetch_tests_between_period_date(){

        return $this->usersInterface->fetch_tests_between_period_date();
    }
}
