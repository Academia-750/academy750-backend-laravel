<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Resources\ImportProcesses\v1\Interfaces\ImportProcessesInterface;
use App\Http\Controllers\Controller;
use App\Models\ImportProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @group Import
 *
 * ?? Not sure what is this for
 */
class ImportProcessesController extends Controller
{
    protected ImportProcessesInterface $importProcessesInterface;

    public function __construct(ImportProcessesInterface $importProcessesInterface)
    {
        $this->importProcessesInterface = $importProcessesInterface;
    }

    public function index()
    {
        return $this->importProcessesInterface->index();
    }

    public function get_relationship_import_records(ImportProcess $import_process)
    {
        return $this->importProcessesInterface->get_relationship_import_records($import_process);
    }
}