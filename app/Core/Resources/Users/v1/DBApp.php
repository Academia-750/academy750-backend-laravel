<?php
namespace App\Core\Resources\Users\v1;

use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Core\Resources\Users\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Users\v1\Services\ActionsAccountUser;
use App\Core\Resources\Users\v1\Services\StatisticsDataHistoryStudent;
use App\Core\Resources\Users\v1\Services\TopicsStatisticsService;
use App\Core\Services\UserService;
use App\Exports\Api\Users\v1\UsersExport;
use App\Models\Question;
use App\Models\Role;
use App\Models\Test;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\Api\ContactUsHomeNotification;
use App\Notifications\Api\ResetPasswordStudentNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

//use App\Imports\Api\Users\v1\UserImport;



class DBApp implements UsersInterface
{
    protected User $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function index()
    {
        return $this->model::applyFilters()
            ->applySorts()
            ->applyIncludes()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->jsonPaginate();
    }

    public function create($request): array
    {
        try {
            $secureRandomPassword = UserService::generateSecureRandomPassword();

            DB::beginTransaction();
            $userCreated = $this->model->query()->create([
                'dni' => $request->get('dni'),
                'first_name' => $request->get('first-name'),
                'last_name' => $request->get('last-name'),
                'full_name' => "{$request->get('first-name')} {$request->get('last-name')}",
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'password' => Hash::make($secureRandomPassword)
            ]);

            $defaultRole = Role::defaultRole();

            if (!$defaultRole) {
                abort(500, 'Default Role not set');
            }
            $userCreated->roles()->save($defaultRole);


            DB::commit();

            return [
                'user' => $this->model->applyIncludes()->findOrFail($userCreated->getKey()),
                'password_generated' => $secureRandomPassword
            ];

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function read($user)
    {
        return $this->model->applyIncludes()->findOrFail($user->getKey());
    }

    public function update($request, $user): \App\Models\User
    {
        try {

            DB::beginTransaction();
            $first_name = $request->get('first-name') ?? $user->first_name;
            $last_name = $request->get('last-name') ?? $user->last_name;

            $user->dni = $request->get('dni') ?? $user->dni;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->full_name = "{$first_name} {$last_name}";
            $user->phone = $request->get('phone') ?? $user->phone;
            $user->email = $request->get('email') ?? $user->email;
            $user->save();

            UserService::syncRolesToUser(
                $request->get('roles'),
                $user
            );


            DB::commit();

            return $this->model->applyIncludes()->findOrFail($user->getKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete($user): void
    {
        try {

            DB::beginTransaction();
            ActionsAccountUser::deleteUser($user);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function mass_selection_for_action($request): array
    {
        try {

            DB::beginTransaction();

            $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('users'));

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

    public function import_records($request): string
    {
        return "Proceso de importación iniciado";
    }

    public function disable_account($request, $user)
    {
        return ActionsAccountUser::disableAccountUser($user);
    }

    public function enable_account($request, $user)
    {
        return ActionsAccountUser::enableAccountUser($user);
    }

    public function contactsUS($request)
    {

        try {

            $userAcademia = User::query()->firstWhere('email', '=', config('mail.from.address'));

            if (!$userAcademia) {
                abort(500, 'No se puede encontrar el correo de la academia');
            }

            if ($request->get('reason') === 'general' || $request->get('reason') === 'inscription') {

                $userAcademia->notify(new ContactUsHomeNotification([
                    'reason' => __("messages.{$request->get('reason')}"),
                    'firstName' => $request->get('first-name'),
                    'lastName' => $request->get('last-name'),
                    'phone' => $request->get('phone'),
                    'email' => $request->get('email'),
                    'message' => $request->get('message'),
                ]));

                if ($request->get('reason') === 'general') {
                    return [
                        'status' => 'successfully',
                        'message' => '¡Muchas gracias por su solicitud! Nos pondremos en contacto con usted lo antes posible.'
                    ];
                }

                if ($request->get('reason') === 'inscription') {
                    return [
                        'status' => 'successfully',
                        'message' => '¡Primer paso dado! Nos pondremos en contacto con usted lo antes posible. ¡Te esperamos con los brazos abiertos!'
                    ];
                }
            }

            DB::beginTransaction();
            if ($request->get('reason') === 'reset-password') {
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

            return [
                'status' => 'failed',
                'message' => 'No se ha realizado ninguna acción'
            ];

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function get_history_statistical_data_graph_by_student($request)
    {
        try {

            $today = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
            $student = Auth::user();
            $last_date = Carbon::parse(date('Y-m-d', strtotime($today . StatisticsDataHistoryStudent::getPeriodInKey($request->get('period')))))->endOfDay()->format('Y-m-d H:i:s');

            $topicsData = StatisticsDataHistoryStudent::getCollectGroupsStatisticsQuestionsTopic(
                $request->get('topics_id'),
                $request->get('period'),
                [
                    'student_id' => $student?->getKey(),
                    'last_date' => $last_date,
                    'today' => $today,
                ]
            );

            $topics = [];

            foreach ($topicsData as $topicData) {
                $topicDataArray = (array) $topicData;

                $topics[] = [
                    'topic' => Topic::query()->findOrFail($topicDataArray['TOPIC_ID']),
                    'correct' => $topicDataArray['CORRECT_ANS'],
                    'wrong' => $topicDataArray['INCORRECT_ANS'],
                    'unanswered' => $topicDataArray['UNANSWERED_ANS'],
                ];
            }

            return $topics;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function fetch_history_questions_by_type_and_period()
    {
        try {
            $student = Auth::user();

            if (!$student) {
                abort(500, 'No se encuentra al usuario autenticado');
            }

            $test_id_request = request('test-id');

            $test = Test::query()
                ->where('uuid', $test_id_request)
                ->first();

            abort_if(!$test, new ModelNotFoundException("No existe el Test o cuestionario con identificador {$test_id_request}"));
            // return [];

            return Question::query()->whereIn(
                'id',
                $test->questions()->wherePivot(
                    'status_solved_question',
                    '=',
                    request('type-question')
                )->pluck('questions.id')->toArray()
            )->jsonPaginate();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function fetch_history_questions_wrong_by_topic_of_student($topic)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                abort(404);
            }

            //$questions_id  = array_unique(TopicsStatisticsService::getQuestionsFailedBelongsToTopicAndTest($topic));
            $questions_id_results_procedure = DB::select(
                'call get_questions_wrong_history_by_topic_and_tests_student_procedure(?,?)',
                array(
                    $user->getKey(), $topic->getKey()
                )
            );

            return array_map(static function ($item) {
                $questionItem = (array) $item;
                return [
                    'question_data' => Question::query()->findOrFail($questionItem['question_wrong_id']),
                    'explicacion_pregunta' => $questionItem['explicacion_pregunta'],
                    'fecha_test' => $questionItem['fecha_test'],
                    'question_wrong_id' => $questionItem['question_wrong_id'],
                    'test_wrong_id' => $questionItem['test_wrong_id'],
                    'texto_pregunta' => $questionItem['texto_pregunta'],
                    'texto_respuesta_correcta' => $questionItem['texto_respuesta_correcta'],
                    'texto_respuesta_marcada' => $questionItem['texto_respuesta_marcada']
                ];
            }, $questions_id_results_procedure);

        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function fetch_history_tests_completed_by_student()
    {
        try {

            return Auth::user()?->tests()
                ->where('test_type', '=', 'test')
                ->where('is_solved_test', '=', 'yes')
                ->applyFilters()
                ->applySorts()
                ->applyIncludes()
                ->jsonPaginate();
        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function fetch_topics_available_in_tests()
    {
        try {

            $topicsData = array_unique(
                TopicsStatisticsService::getTopicsByTestsCompleted()
            );

            return Topic::query()
                ->whereIn('id', $topicsData)
                ->applyFilters()
                ->applySorts()
                ->applyIncludes()
                ->jsonPaginate();
        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function fetch_tests_between_period_date()
    {
        try {
            $student = Auth::user();

            if (!$student) {
                abort(500, 'No se encuentra al usuario autenticado');
            }

            $data = [
                'key_period_date' => request('key-period-date')
            ];

            $validateData = Validator::make($data, [
                'key_period_date' => ['required', Rule::in(['all', 'last-month', 'last-three-months'])]
            ]);

            if ($validateData->fails()) {
                abort(400, 'Por favor, mandar el parámetro correcto para filtrar por periodo');
            }

            if (request('key-period-date') === 'all') {
                return $student?->tests()
                    ->where('test_type', '=', 'test')
                    ->where('is_solved_test', '=', 'yes')
                    ->applyFilters()
                    ->applySorts()
                    ->applyIncludes()
                    ->jsonPaginate();
            }

            $today = date('Y-m-d');

            $last_date = date(
                'Y-m-d',
                strtotime(
                    $today . StatisticsDataHistoryStudent::getPeriodInKey(request('key-period-date'))
                )
            );

            $tests_id_by_procedure = DB::select(
                'call get_tests_of_student_by_period_date_procedure(?,?,?)',
                array($student->getKey(), $last_date, $today)
            );


            $tests_id = array_map(static function ($test) {
                $test = (array) $test;

                return $test['id'];
            }, $tests_id_by_procedure);


            return Test::query()
                ->where('test_type', '=', 'test')
                ->where('is_solved_test', '=', 'yes')
                ->whereIn('id', $tests_id)
                ->applyFilters()
                ->applySorts()
                ->applyIncludes()
                ->jsonPaginate();

        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }
}