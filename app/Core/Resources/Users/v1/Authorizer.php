<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class Authorizer implements UsersInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(\App\Core\Resources\Users\v1\SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(){
        Gate::authorize('index', User::class );
        return $this->schemaJson->index();
    }

    public function create( $request ){
        Gate::authorize('create', User::class );
        return $this->schemaJson->create($request);
    }

    public function read( $user ){
        Gate::authorize('read', $user );
        return $this->schemaJson->read( $user );
    }

    public function update( $request, $user ){
        Gate::authorize('update', $user );
        return $this->schemaJson->update( $request, $user );
    }

    public function delete( $user ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $user );
        return $this->schemaJson->delete( $user );
    }

    public function mass_selection_for_action( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', User::class );
        return $this->schemaJson->mass_selection_for_action( $request );
    }

    public function lock_account( $request, $user )
    {
        Gate::authorize('lock_account', $user );
        return $this->schemaJson->lock_account( $request, $user );
    }

    public function unlock_account( $request, $user )
    {
        Gate::authorize('unlock_account', $user );
        return $this->schemaJson->unlock_account( $request, $user );
    }

    public function export_records( $request ){
        Gate::authorize('export_records', User::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ){
        Gate::authorize('import_records', User::class );
        return $this->schemaJson->import_records( $request );
    }

}
