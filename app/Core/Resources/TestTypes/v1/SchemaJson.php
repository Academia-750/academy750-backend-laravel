<?php
namespace App\Core\Resources\TestTypes\v1;

use App\Models\TestType;
use App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface;
use App\Http\Resources\Api\TestType\v1\TestTypeCollection;
use App\Http\Resources\Api\TestType\v1\TestTypeResource;
use App\Core\Resources\TestTypes\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TestTypesInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): TestTypeCollection
    {
        return TestTypeCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return TestTypeResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $test_type ): TestTypeResource
    {
        return TestTypeResource::make(
            $this->eventApp->read( $test_type )
        );
    }

    public function update( $request, $test_type ): TestTypeResource
    {
        return TestTypeResource::make(
            $this->eventApp->update( $request, $test_type )
        );
    }

    public function delete( $test_type ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $test_type );
        return response()->noContent();
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'information' => $this->eventApp->action_for_multiple_records( $request )
        ], 200);
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->export_records( $request );

        return response()->json([
            'message' => "Proceso de exportación iniciada"
        ], 200);
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->import_records( $request );

        return response()->json([
            'message' => "Proceso de importación iniciada"
        ], 200);
    }

}
