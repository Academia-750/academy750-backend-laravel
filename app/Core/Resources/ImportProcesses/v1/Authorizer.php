<?php
namespace App\Core\Resources\ImportProcesses\v1;

use App\Models\ImportProcess;
use App\Core\Resources\ImportProcesses\v1\Interfaces\ImportProcessesInterface;
use App\Http\Resources\Api\ImportProcess\v1\ImportProcessCollection;
use App\Http\Resources\Api\ImportProcess\v1\ImportProcessResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\ImportProcesses\v1\SchemaJson;
class Authorizer implements ImportProcessesInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): ImportProcessCollection
    {
        Gate::authorize('index', ImportProcess::class );
        return $this->schemaJson->index();
    }

    public function get_relationship_import_records($import_process)
    {
        Gate::authorize('get_relationship_import_records', $import_process );
        return $this->schemaJson->get_relationship_import_records( $import_process );
    }
}
