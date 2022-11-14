<?php
namespace App\Core\Resources\Users\v1;

use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Core\Resources\Users\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Users\v1\Services\ActionsAccountUser;
use App\Core\Services\UserService;
use App\Exports\Api\Users\v1\UsersExport;
use App\Models\Role;
use App\Models\User;
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

    public function create( $request ): \App\Models\User{
        try {
            $secureRandomPassword = UserService::generateSecureRandomPassword();

            DB::beginTransaction();
                $userCreated = $this->model->query()->create([
                    'dni' => $request->get('dni'),
                    'first_name' => $request->get('first-name'),
                    'last_name' => $request->get('last-name'),
                    'phone' => $request->get('phone'),
                    'email' => $request->get('email'),
                    'password' => Hash::make($secureRandomPassword)
                ]);

                UserService::syncRolesToUser(
                    $request->get('roles'),
                    $userCreated
                );

            DB::commit();

            return $this->model->applyIncludes()->find($userCreated->id);

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
                $user->dni = $request->get('dni') ?? $user->dni;
                $user->first_name = $request->get('first-name') ?? $user->first_name;
                $user->last_name = $request->get('last-name') ?? $user->last_name;
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
}
