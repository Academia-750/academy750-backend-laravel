<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Http\Resources\Api\Test\v1\TestCollection;
use App\Http\Resources\Api\Test\v1\TestResource;
use App\Core\Resources\Tests\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TestsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): TestCollection
    {
        return TestCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return TestResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $test ): TestResource
    {
        return TestResource::make(
            $this->eventApp->read( $test )
        );
    }

    public function update( $request, $test ): TestResource
    {
        return TestResource::make(
            $this->eventApp->update( $request, $test )
        );
    }

    public function delete( $test ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $test );
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
