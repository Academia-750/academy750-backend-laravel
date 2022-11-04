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
    protected UsersInterface $UsersInterface;

    public function __construct(UsersInterface $UsersInterface ){
        $this->UsersInterface = $UsersInterface;
    }

    public function index(){
        return $this->UsersInterface->index();
    }

    public function create(CreateUserRequest $request){
        return $this->UsersInterface->create($request);
    }

    public function read(User $User){
        return $this->UsersInterface->read( $User );
    }

    public function update(UpdateUserRequest $request, User $User){
        return $this->UsersInterface->update( $request, $User );
    }

    public function delete(User $User){
        return $this->UsersInterface->delete( $User );
    }

    public function mass_selection_for_action(ActionForMassiveSelectionUsersRequest $request): string{
        return $this->UsersInterface->mass_selection_for_action( $request );
    }

    public function export_records(ExportUsersRequest $request){
        return $this->UsersInterface->export_records( $request );
    }

    public function import_records(ImportUsersRequest $request){
        return $this->UsersInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/User.csv', 'template_import_User');
    }
}
