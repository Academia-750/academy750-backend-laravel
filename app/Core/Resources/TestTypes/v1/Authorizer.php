<?php
namespace App\Core\Resources\TestTypes\v1;

use App\Models\TestType;
use App\Core\Resources\TestTypes\v1\Interfaces\TestTypesInterface;
use App\Http\Resources\Api\TestType\v1\TestTypeCollection;
use App\Http\Resources\Api\TestType\v1\TestTypeResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\TestTypes\v1\SchemaJson;
class Authorizer implements TestTypesInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): TestTypeCollection
    {
        Gate::authorize('index', TestType::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', TestType::class );
        return $this->schemaJson->create($request);
    }

    public function read( $test_type ): TestTypeResource
    {
        Gate::authorize('read', $test_type );
        return $this->schemaJson->read( $test_type );
    }

    public function update( $request, $test_type ): TestTypeResource
    {
        Gate::authorize('update', $test_type );
        return $this->schemaJson->update( $request, $test_type );
    }

    public function delete( $test_type ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $test_type );
        return $this->schemaJson->delete( $test_type );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', TestType::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', TestType::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', TestType::class );
        return $this->schemaJson->import_records( $request );
    }

}
