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

    public function index(): \App\Http\Resources\Api\User\v1\UserCollection
    {
        Gate::authorize('index', User::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', User::class );
        return $this->schemaJson->create($request);
    }

    public function read( $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('read', $user );
        return $this->schemaJson->read( $user );
    }

    public function update( $request, $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
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

    public function disable_account( $request, $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('disable_account', $user );
        return $this->schemaJson->disable_account( $request, $user );
    }

    public function enable_account( $request, $user ): \App\Http\Resources\Api\User\v1\UserResource
    {
        Gate::authorize('enable_account', $user );
        return $this->schemaJson->enable_account( $request, $user );
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