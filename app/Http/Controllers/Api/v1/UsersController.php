<?php
namespace App\Http\Controllers\Api\v1;

use App\Core\Resources\Users\v1\Services\ActionsAccountUser;
use App\Core\Services\UserService;
use App\Http\Requests\Api\v1\Users\ContactUsPageRequest;
use App\Http\Requests\Api\v1\Users\FetchHistoryQuestionsByTypeAndPeriodOfStudentRequest;
use App\Http\Requests\Api\v1\Users\FetchHistoryStatisticalDataGraphByStudentRequest;
use App\Http\Requests\Api\v1\Users\SearchUserRequest;
use App\Models\Topic;
use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Http\Controllers\Controller;
use App\Notifications\Api\ResetPasswordStudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function __construct(UsersInterface $usersInterface)
    {
        $this->usersInterface = $usersInterface;
    }

    public function index()
    {
        return $this->usersInterface->index();
    }

    public function create(CreateUserRequest $request)
    {
        return $this->usersInterface->create($request);
    }

    public function read(User $user)
    {
        return $this->usersInterface->read($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        return $this->usersInterface->update($request, $user);
    }

    public function delete(User $user)
    {
        return $this->usersInterface->delete($user);
    }

    public function mass_selection_for_action(ActionForMassiveSelectionUsersRequest $request): string
    {
        return $this->usersInterface->mass_selection_for_action($request);
    }

    public function disable_account(Request $request, User $user)
    {
        return $this->usersInterface->disable_account($request, $user);
    }

    public function enable_account(Request $request, User $user)
    {
        return $this->usersInterface->enable_account($request, $user);
    }

    public function contactsUS(ContactUsPageRequest $request)
    {
        return $this->usersInterface->contactsUS($request);
    }

    public function requestResetPasswordUser(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255']
        ]);

        $student = User::firstWhere('email', '=', $request->get('email'));

        if (!$student || $student->hasRole('admin')) {
            return [
                'status' => 'failed',
                'message' => 'No se encontró al usuario'
            ];
        }

        if (!$student->hasRole('student')) {
            return [
                'status' => 'failed',
                'message' => 'No es válido el correo electrónico'
            ];
        }

        $password_generated = UserService::generateSecureRandomPassword();

        $student->password = Hash::make($password_generated);
        $student->save();

        DB::table('password_resets')->where('email', $student->email)->delete();
        ActionsAccountUser::removeAllTokensByUserReally($student);

        DB::commit();
        $student->notify(new ResetPasswordStudentNotification(compact('password_generated')));

        return [
            'status' => 'successfully',
            'message' => 'Hemos enviado sus nuevas credenciales de acceso al correo solicitado.'
        ];
    }

    public function fetch_history_statistical_data_graph_by_student(FetchHistoryStatisticalDataGraphByStudentRequest $request)
    {

        return $this->usersInterface->get_history_statistical_data_graph_by_student($request);
    }

    public function fetch_history_questions_by_type_and_period()
    {

        return $this->usersInterface->fetch_history_questions_by_type_and_period();
    }

    public function fetch_history_questions_wrong_by_topic_of_student(Topic $topic)
    {

        return $this->usersInterface->fetch_history_questions_wrong_by_topic_of_student($topic);
    }

    public function fetch_history_tests_completed_by_student()
    {

        return $this->usersInterface->fetch_history_tests_completed_by_student();
    }

    public function fetch_topics_available_in_tests()
    {

        return $this->usersInterface->fetch_topics_available_in_tests();
    }

    public function fetch_tests_between_period_date()
    {

        return $this->usersInterface->fetch_tests_between_period_date();
    }



    public function search(SearchUserRequest $request)
    {
        try {
            $conditions = removeNull([
                parseFilter(['first_name', 'last_name', 'dni'], $request->get('content'), 'or_like')
            ]);


            $query = User::query()->where(function ($query) use ($conditions) {
                foreach ($conditions as $condition) {
                    $condition($query);
                }
            });


            $results = $query
                ->select('id', 'first_name', 'last_name', 'dni', 'uuid')
                ->orderBy('created_at', 'desc')
                ->limit($request->get('limit') ?? 20)
                ->get();

            return response()->json([
                'status' => 'successfully',
                'results' => $results,
            ]);


        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }
}