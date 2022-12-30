<?php
namespace App\Core\Resources\Users\v1;

use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Core\Resources\Users\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Users\v1\Services\ActionsAccountUser;
use App\Core\Resources\Users\v1\Services\StatisticsDataHistoryStudent;
use App\Core\Services\UserService;
use App\Exports\Api\Users\v1\UsersExport;
use App\Models\Role;
use App\Models\Test;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\Api\ContactUsHomeNotification;
use App\Notifications\Api\ResetPasswordStudentNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\Api\Users\v1\UserImport;



class DBApp implements UsersInterface
{
    protected User $model;

    public function __construct(User $user ){
        $this->model = $user;
    }

    public function index(){
        return $this->model::applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): array{
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

                UserService::syncRolesToUser(
                    $request->get('roles'),
                    $userCreated
                );

            DB::commit();

            return [
                'user' => $this->model->applyIncludes()->find($userCreated->id),
                'password_generated' => $secureRandomPassword
            ];

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $user ){
        //dump($user->id);
        return $this->model->applyIncludes()->find($user->getRouteKey());
    }

    public function update( $request, $user ): \App\Models\User{
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

            return $this->model->applyIncludes()->find($user->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $user ): void{
        try {

            DB::beginTransaction();
            ActionsAccountUser::deleteUser($user);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function mass_selection_for_action( $request ): array{
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

    public function export_records( $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse{
        if ($request->get('type') === 'pdf') {
            $domPDF = App::make('dompdf.wrapper');
            $users = $this->model->query()->whereIn('id', $request->get('students'))->get();
            $domPDF->loadView('resources.export.templates.pdf.students', compact('users'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-students.pdf');
        }
        return Excel::download(new UsersExport($request->get('students')), 'students.'. $request->get('type'));
    }

    public function import_records( $request ): string{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new UserImport(Auth::user()))->import($request->file('students'));

         /*
         // Lanzamiento de errores en caso de validacion sin uso de Queues
         if ($importInstance->failures()->isNotEmpty()) {
             throw ValidationException::withMessages([
                 'errors' => [
                     $importInstance->failures()
                 ]
             ]);
         }*/
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
                $student = User::firstWhere('email', '=', $request->get('email'));

                if (!$student) {
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
                DB::table('personal_access_tokens')->where('tokenable_id', '=', $student->getRouteKey())->delete();

                DB::commit();
                $student->notify(new ResetPasswordStudentNotification(compact('password_generated')));

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

            $today = date('Y-m-d');
            $student = Auth::user();
            $last_date = date('Y-m-d', strtotime($today . StatisticsDataHistoryStudent::getPeriodInKey( $request->get('period') )));

            $topicsData = StatisticsDataHistoryStudent::getCollectGroupsStatisticsQuestionsTopic(
                $request->get('topics_id'),
                $request->get('period'), [
                    'student_id' => $student?->getRouteKey(),
                    'last_date' => $last_date,
                    'today' => $today,
                ]
            );

            $topics = [];

            foreach ($topicsData as $topicData) {
                $topicDataArray = (array) $topicData;

                 $topics[] = [
                    'topic' => Topic::query()->find($topicDataArray['topic_id']),
                    'correct' => $topicDataArray['correct'],
                    'wrong' => $topicDataArray['wrong'],
                    'unanswered' => $topicDataArray['unanswered'],
                ];
            }

            return $topics;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function fetch_history_questions_by_type_and_period($request)
    {
        try {


        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function fetch_history_questions_wrong_by_topic_of_student($topic)
    {
        try {


        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function fetch_history_tests_completed_by_student()
    {
        try {

            return Auth::user()?->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'yes')->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function fetch_topics_available_in_tests()
    {
        try {
            $topicsData = [];

            $tests = Auth::user()?->tests()->where('test_type', '=', 'test')->where('is_solved_test', '=', 'yes')->get();

            foreach ($tests as $test) {

                foreach ($test->topics()->pluck("topics.id")->toArray() as $topic) {
                    $topicsData[] = $topic;
                }

            }

            return Topic::query()->whereIn('id', array_unique($topicsData))->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
        } catch (\Exception $e) {
            DB::rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }
}
