<?php
namespace App\Core\Resources\ImportProcesses\v1\Interfaces;

use App\Models\ImportProcess;

interface ImportProcessesInterface
{
    public function index();
    public function get_relationship_import_records( $import_process );
}
