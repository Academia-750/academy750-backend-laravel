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

    public function registerImportProcessHistory ( $userAuth, $nameFile, $category ): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
    {
         $importProcessRecord = ImportProcess::query()->create([
            "name_file" => $nameFile,
            "user_id" => $userAuth->getKey(),
            "category" => $category,
            "total_number_of_records" => '0',
            "total_number_failed_records" => '0',
            "total_number_successful_records" => '0',
            "status_process_file" => 'pending',
        ]);

        $this->importProcessRecord = $importProcessRecord;
        $this->userAuth = $userAuth;

        return $importProcessRecord;
    }

    public function registerImportRecordHistory ( $data ): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        return ImportRecord::query()->create([
            "number_of_row" => $data["current-row"],
            "has_errors" => $data["has-errors"] ? 'yes' : 'no',
            "errors_validation" => $data["errors-validation"],
            "import_process_id" => $data["import-process-id"],
        ]);
    }

    public function getCurrentRow () {
        $importProcess = ImportProcess::query()->findOrFail($this->importProcessRecord->id);

        if (!$importProcess) {
            return 0;
        }

        $this->count_row_current_sheet++;
        return ($importProcess->total_number_of_records + $this->count_row_current_sheet) + 1;
    }

    public function updateDataImportHistory ($event): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $importProcess = ImportProcess::query()->findOrFail($event->getConcernable()->importProcessRecord->id);
        $importProcess->total_number_of_records = (int) $importProcess->total_number_of_records + (int) $event->getConcernable()->count_row_current_sheet;
        $importProcess->total_number_successful_records = (int) $importProcess->total_number_successful_records + (int) $event->getConcernable()->count_rows_successfully;
        $importProcess->total_number_failed_records = (int) $importProcess->total_number_failed_records + (int) $event->getConcernable()->count_rows_failed;
        $importProcess->status_process_file = "pending";
        $importProcess->save();

        return $importProcess;
    }

    public function setStatusCompleteImportHistory ($event): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $importProcess = ImportProcess::query()->findOrFail($event->getConcernable()->importProcessRecord->id);
        $importProcess->status_process_file = "complete";
        $importProcess->save();

        return $importProcess;
    }

}
