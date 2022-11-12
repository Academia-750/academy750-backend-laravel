<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\Tests\v1\CacheApp;
class EventApp implements TestsInterface
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
        /* broadcast(new CreateTestEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $test ){
        return $this->cacheApp->read( $test );
    }

    public function update( $request, $test ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $test );
        /* broadcast(new UpdateTestEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $test ): void{
        /* broadcast(new DeleteTestEvent($test)); */
        $this->cacheApp->delete( $test );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = Test::whereIn('id', $request->get('tests'));

        broadcast(
            new ActionForMassiveSelectionTestEvent( $request->get('action'), $records )
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
