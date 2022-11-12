<?php
namespace App\Core\Resources\TestTypes\v1;

use App\Models\TestType;
use App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Core\Resources\TestTypes\v1\CacheApp;
class EventApp implements TestTypesInterface
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
        /* broadcast(new CreateTestTypeEvent($itemCreatedInstance)); */
        return $itemCreatedInstance;
    }

    public function read( $test_type ){
        return $this->cacheApp->read( $test_type );
    }

    public function update( $request, $test_type ){
        $itemUpdatedInstance = $this->cacheApp->update( $request, $test_type );
        /* broadcast(new UpdateTestTypeEvent($itemUpdatedInstance)); */
        return $itemUpdatedInstance;
    }

    public function delete( $test_type ): void{
        /* broadcast(new DeleteTestTypeEvent($test_type)); */
        $this->cacheApp->delete( $test_type );
    }

    public function action_for_multiple_records( $request ): array{

        /* $records = TestType::whereIn('id', $request->get('test_types'));

        broadcast(
            new ActionForMassiveSelectionTestTypeEvent( $request->get('action'), $records )
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
