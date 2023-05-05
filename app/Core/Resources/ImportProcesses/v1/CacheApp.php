<?php
namespace App\Core\Resources\ImportProcesses\v1;

use App\Core\Resources\ImportProcesses\v1\Interfaces\ImportProcessesInterface;
class CacheApp implements ImportProcessesInterface
{
    protected DBApp $dbApp;

    public function __construct(DBApp $dbApp ){
        $this->dbApp = $dbApp;
    }

    public function index()
    {
        return $this->dbApp->index();
    }

    /**
     * @throws \JsonException
     */
    public function get_relationship_import_records($import_process)
    {
        return $this->dbApp->get_relationship_import_records($import_process);
    }
}
