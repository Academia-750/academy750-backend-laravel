<?php
namespace App\Http\Controllers\Api\v1;

use App\Core\Resources\Users\v1\Services\ActionsAccountUser;
use App\Core\Services\UserService;
use App\Http\Controllers\JsonApiAuth\Revokers\SanctumRevoker;
use App\Http\Requests\Api\v1\Users\ContactUsPageRequest;
use App\Http\Requests\Api\v1\Users\FetchHistoryQuestionsByTypeAndPeriodOfStudentRequest;
use App\Http\Requests\Api\v1\Users\FetchHistoryStatisticalDataGraphByStudentRequest;
use App\Http\Requests\Api\v1\Users\SearchUserRequest;
use App\Http\Requests\Api\v1\Users\UserRoleUpdateRequest;
use App\Models\Role;
use App\Models\Topic;
use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Http\Controllers\Controller;
use App\Notifications\Api\ResetPasswordStudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Users\CreateUserRequest;
use App\Http\Requests\Api\v1\Users\UpdateUserRequest;
use App\Http\Requests\Api\v1\Users\ActionForMassiveSelectionUsersRequest;
use App\Http\Requests\Api\v1\Users\ExportUsersRequest;
use App\Http\Requests\Api\v1\Users\ImportUsersRequest;
use Illuminate\Validation\Rule;

/**
 * @group Users
 *
 * APIs for managing opposition users
 */
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

        $user = User::firstWhere('email', '=', $request->get('email'));

        if (!$user) {
            return [
                'status' => 'failed',
                'message' => 'No se encontró al usuario'
            ];
        }



        $password_generated = UserService::generateSecureRandomPassword();

        $user->password = Hash::make($password_generated);
        $user->save();

        DB::table('password_resets')->where('email', $user->email)->delete();
        ActionsAccountUser::removeAllTokensByUserReally($user);

        DB::commit();
        $user->notify(new ResetPasswordStudentNotification(compact('password_generated')));

        return [
            'status' => 'successfully',
            'message' => 'Hemos enviado sus nuevas credenciales de acceso al correo solicitado.'
        ];
    }

    /**
     * Tests: History Graph
     *
     * History Data Graph by topic
     */
    public function fetch_history_statistical_data_graph_by_student(FetchHistoryStatisticalDataGraphByStudentRequest $request)
    {

        return $this->usersInterface->get_history_statistical_data_graph_by_student($request);
    }

    /**
     * Tests: History By Type
     *
     * History Data by type and period
     */
    public function fetch_history_questions_by_type_and_period()
    {

        return $this->usersInterface->fetch_history_questions_by_type_and_period();
    }

    /**
     * Tests: Wrong Questions
     *
     * History of questions answered wrong
     */
    public function fetch_history_questions_wrong_by_topic_of_student(Topic $topic)
    {

        return $this->usersInterface->fetch_history_questions_wrong_by_topic_of_student($topic);
    }

    /**
     * Tests: History Completed Tests
     *
     * History of tests completed by student
     * @authenticated
     */
    public function fetch_history_tests_completed_by_student()
    {

        return $this->usersInterface->fetch_history_tests_completed_by_student();
    }

    public function fetch_topics_available_in_tests()
    {

        return $this->usersInterface->fetch_topics_available_in_tests();
    }

    /**
     * Tests: History Test between dates
     *
     * @authenticated
     */
    public function fetch_tests_between_period_date()
    {

        return $this->usersInterface->fetch_tests_between_period_date();
    }


    /**
     * Users: Search
     *
     * Search for users (Normally for auto complete purposes)
     * Only for Admin
     * @authenticated
     * @response {
     *     "results": [
     *        "id" : "1" ,
     *        "uuid" : "uuid" ,
     *        "first_name" : "Son" ,
     *        "last_name" : "Go Ku" ,
     *        "dni" : "74370249W" ,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ]
     *  }
     */
    public function search(SearchUserRequest $request)
    {
        try {
            $conditions = removeNull([
                // Can add as a property if required
                parseFilter('state', 'disable', '!='),
                // May need to change when we add the profiles ENDPOINT
                parseFilter('roles', 'admin', 'notHave', ['field' => 'name']),
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
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }


    /**
     * Users: Change Role
     *
     * Changes the role of a user
     * Only for Admin
     * @authenticated
     * @response {
     *     "message": "successfully"
     *  }
     * @response status=404 scenario="User not found"
     * @response status=404 scenario="Role not found"
     * @response status=409 scenario="Cant assign the role admin"
     * @response status=403 scenario="Cant update an admin user"
     */
    public function changeRole(UserRoleUpdateRequest $request)
    {
        try {

            $user = User::where('uuid', $request->get('user_id'))->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'User not found'
                ], 404);
            }

            if ($user->hasRole('admin')) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Can`t change the role of an admin user'
                ], 403);
            }


            $role = Role::find($request->get('role_id'));

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Role not found'
                ], 404);
            }

            if ($role->name === 'admin') {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Not allowed to set role admin in this version'
                ], 409);
            }

            $user->roles()->sync($role);

            // Force the user to reload
            (new SanctumRevoker($user))->deleteAllTokens();

            return response()->json([
                'status' => 'successfully',
            ]);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }
}