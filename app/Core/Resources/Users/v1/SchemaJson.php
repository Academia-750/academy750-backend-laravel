<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Http\Resources\Api\User\v1\UserCollection;
use App\Http\Resources\Api\User\v1\UserResource;
use Illuminate\Support\Str;

class SchemaJson implements UsersInterface
{
    protected EventApp $eventApp;

    public function __construct(\App\Core\Resources\Users\v1\EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): UserCollection
    {
        return UserCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ){
        return UserResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $user ){
        return UserResource::make(
            $this->eventApp->read( $user )
        );
    }

    public function update( $request, $user ){
        return UserResource::make(
            $this->eventApp->update( $request, $user )
        );
    }

    public function delete( $user ){
        return response()->json($this->eventApp->delete( $user ), 204);
    }

    public function mass_selection_for_action( $request ): string{
        return response()->json([
            'message' => $this->eventApp->mass_selection_for_action( $request )
        ], 200);
    }

    public function export_records( $request ){
        return $this->eventApp->export_records( $request );
    }

    public function import_records( $request ){
        return $this->eventApp->import_records( $request );
    }

}
