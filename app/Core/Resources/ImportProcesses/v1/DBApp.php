<?php
namespace App\Core\Resources\ImportProcesses\v1;

use App\Models\ImportProcess;
use App\Core\Resources\ImportProcesses\v1\Interfaces\ImportProcessesInterface;

class DBApp implements ImportProcessesInterface
{
    protected ImportProcess $model;

    public function __construct(ImportProcess $import_process ){
        $this->model = $import_process;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function get_relationship_import_records($import_process)
    {
        return $import_process->import_records()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }
}
