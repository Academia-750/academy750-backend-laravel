<?php
namespace App\Core\Resources\ImportProcesses\v1;

use App\Http\Resources\Api\ImportRecord\v1\ImportRecordCollection;
use App\Core\Resources\ImportProcesses\v1\Interfaces\ImportProcessesInterface;
use App\Http\Resources\Api\ImportProcess\v1\ImportProcessCollection;
use App\Http\Resources\Api\ImportProcess\v1\ImportProcessResource;

class SchemaJson implements ImportProcessesInterface
{
    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp)
    {
        $this->cacheApp = $cacheApp;
    }

    public function index(): ImportProcessCollection
    {
        return ImportProcessCollection::make(
            $this->cacheApp->index()
        );
    }

    public function get_relationship_import_records($import_process)
    {
        return ImportRecordCollection::make(
            $this->cacheApp->get_relationship_import_records($import_process)
        )->additional([
                    'meta' => [
                        'import_process' => ImportProcessResource::make($import_process)
                    ]
                ]);
    }

}