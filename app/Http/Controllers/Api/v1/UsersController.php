<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Users\CreateUserRequest;
use App\Http\Requests\Api\v1\Users\UpdateUserRequest;
use App\Http\Requests\Api\v1\Users\ActionForMassiveSelectionUsersRequest;
use App\Http\Requests\Api\v1\Users\ExportUsersRequest;
use App\Http\Requests\Api\v1\Users\ImportUsersRequest;

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

    public function lock_account($request, User $user){
        return $this->usersInterface->lock_account( $request, $user );
    }

    public function unlock_account($request, User $user){
        return $this->usersInterface->unlock_account( $request, $user );
    }

    public function export_records(ExportUsersRequest $request){
        return $this->usersInterface->export_records( $request );
    }

    public function import_records(ImportUsersRequest $request){
        return $this->usersInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/User.csv', 'template_import_User');
    }
}
