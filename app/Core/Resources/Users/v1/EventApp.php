<?php
namespace App\Core\Resources\Users\v1;

use App\Models\User;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventApp implements UsersInterface
{
    protected CacheApp $cacheApp;

    public function __construct(\App\Core\Resources\Users\v1\CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function create( $request ){
        $itemCreatedInstance = $this->cacheApp->create( $request );
        /* broadcast(new CreateUserEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $user ){
        return $this->cacheApp->read( $user );
    }

    public function update( $request, $user ){
        $itemUpdatedInstance = $this->cacheApp->update( $request );
        /* broadcast(new UpdateUserEvent($itemUpdatedInstance)); */
        return $this->cacheApp->update( $request, $user );
    }

    public function delete( $user ){
        /* broadcast(new DeleteUserEvent($user)); */

        return $this->cacheApp->delete( $user );
    }

    public function mass_selection_for_action( $request ): string{

        /* $records = User::whereIn('id', $request->get('students'));

        broadcast(
            new ActionForMassiveSelectionUserEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->mass_selection_for_action( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ){
        $this->cacheApp->import_records( $request );
    }

}
