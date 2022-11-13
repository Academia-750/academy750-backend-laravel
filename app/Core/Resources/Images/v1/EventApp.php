<?php
namespace App\Core\Resources\Images\v1;

use App\Models\Image;
use App\Core\Resources\Images\v1\Interfaces\ImagesInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Images\v1\CacheApp;
class EventApp implements ImagesInterface
{
    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp ){
        $this->cacheApp = $cacheApp;
    }

    public function index(){
        return $this->cacheApp->index();
    }

    public function create( $request ){
        $itemCreatedInstance = $this->cacheApp->create( $request );
        /* broadcast(new CreateImageEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $image ){
        return $this->cacheApp->read( $image );
    }

    public function update( $request, $image ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $image );
        /* broadcast(new UpdateImageEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $image ): void{
        /* broadcast(new DeleteImageEvent($image)); */
        $this->cacheApp->delete( $image );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = Image::whereIn('id', $request->get('images'));

        broadcast(
            new ActionForMassiveSelectionImageEvent( $request->get('action'), $records )
        ); */

        return $this->cacheApp->action_for_multiple_records( $request );
    }

    public function export_records( $request ){
        return $this->cacheApp->export_records( $request );
    }

    public function import_records( $request ): void{
        $this->cacheApp->import_records( $request );
    }

}
