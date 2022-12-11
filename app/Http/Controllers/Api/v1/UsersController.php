<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\v1\Users\ContactUsPageRequest;
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

    public function disable_account(Request $request, User $user){
        return $this->usersInterface->disable_account( $request, $user );
    }

    public function enable_account(Request $request, User $user){
        return $this->usersInterface->enable_account( $request, $user );
    }

    public function contactsUS(ContactUsPageRequest $request){
        return $this->usersInterface->contactsUS( $request );
    }
}
