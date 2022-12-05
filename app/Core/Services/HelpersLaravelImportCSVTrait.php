<?php

namespace App\Core\Services;

use App\Models\ImportProcess;
use App\Models\ImportRecord;

trait HelpersLaravelImportCSVTrait
{
    public $count_row_current_sheet = 0;
    public $count_rows_successfully = 0;
    public $count_rows_failed = 0;
    private $userAuth;
    private $importProcessRecord;

    public function registerImportProcessHistory ( $userAuth, $nameFile ): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
    {
         $importProcessRecord = ImportProcess::query()->create([
            "name_file" => $nameFile,
            "user_id" => $userAuth->getRouteKey(),
            "total_number_of_records" => '0',
            "status_process_file" => 'pending',
        ]);

        $this->importProcessRecord = $importProcessRecord;

        return $importProcessRecord;
    }

    public function registerImportRecordHistory ( $data ): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        return ImportRecord::query()->create([
            "number_of_row" => $data["current-row"],
            "reference_number" => $data["reference-number"],
            "has_errors" => $data["has-errors"] ? 'yes' : 'no',
            "errors_validation" => $data["errors-validation"],
            "import_process_id" => $data["import-process-id"],
        ]);
    }
}
