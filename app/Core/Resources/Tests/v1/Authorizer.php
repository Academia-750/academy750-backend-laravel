<?php
namespace App\Core\Resources\Tests\v1;

use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;
use App\Http\Resources\Api\Test\v1\TestResourceCollection;
use App\Http\Resources\Api\Test\v1\TestModelResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Tests\v1\SchemaJson;
class Authorizer implements TestsInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): TestResourceCollection
    {
        Gate::authorize('index', Test::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Test::class );
        return $this->schemaJson->create($request);
    }

    public function read( $test ): TestModelResource
    {
        Gate::authorize('read', $test );
        return $this->schemaJson->read( $test );
    }

    public function update( $request, $test ): TestModelResource
    {
        Gate::authorize('update', $test );
        return $this->schemaJson->update( $request, $test );
    }

    public function delete( $test ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $test );
        return $this->schemaJson->delete( $test );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Test::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Test::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Test::class );
        return $this->schemaJson->import_records( $request );
    }

}
