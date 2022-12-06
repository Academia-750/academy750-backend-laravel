<?php

namespace App\Imports\Api\v1;

use App\Core\Services\HelpersLaravelImportCSVTrait;
use App\Events\Api\v1\HelloEvent;
use App\Models\ImportProcess;
use App\Models\ImportRecord;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\Api\ImportProcessFileFinishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;

class TopicsImport implements ToCollection, WithHeadingRow, ShouldQueue, WithEvents, WithChunkReading
{
    use Importable, RegistersEventListeners, HelpersLaravelImportCSVTrait;

    public function __construct($userAuth, $nameFile) {
        //$this->userAuth = $userAuth;

        $this->registerImportProcessHistory( $userAuth, $nameFile );
    }

    public function collection(Collection $collection): void {

            $importProcess = ImportProcess::query()->find($this->importProcessRecord->id);


            foreach ($collection as $row) {

                try {
                        $this->count_row_current_sheet++;
                        $current_row = ($importProcess->total_number_of_records + $this->count_row_current_sheet) + 1;

                        $errors = [];
                        $hasErrors = false;

                        $validateData = $this->validateRow($row);

                        if ($validateData->fails()) {
                            $hasErrors = true;
                            $errors = $validateData->errors();
                        }

                    DB::beginTransaction();

                        if (!$hasErrors) {
                            $this->registerTopic($row["tema"], $row["grupo_tema_id"]);
                            $this->count_rows_successfully++;
                        } else {
                            $this->count_rows_failed++;
                        }

                        $this->registerImportRecordHistory([
                            "current-row" => $current_row,
                            'reference-number' => $row["numero_referencia"],
                            "has-errors" => $hasErrors,
                            "errors-validation" => $errors,
                            'import-process-id' => $this->importProcessRecord->id
                        ]);


                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollback();

                    $this->count_rows_failed++;

                    $this->registerImportRecordHistory([
                        "current-row" => $current_row,
                        'reference-number' => $row["numero_referencia"],
                        "has-errors" => true,
                        "errors-validation" => [
                            "topic" => [
                                "Ocurrió un error en el proceso.",
                                $e->getMessage()
                            ]
                        ],
                        'import-process-id' => $this->importProcessRecord->id
                    ]);

                    continue;
                    //broadcast(new FailedImportEvent($e->getMessage(), $this->userAuth));
                    //broadcast(new ImportUsersEvent(array('errors' => [$e->getMessage()]), $this->userAuth));
                }

            }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /*
     * Custom Function
     *
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     * */
    public function validateRow ($row): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make($row->toArray(), [
            'tema' => ['required', 'max:255'],
            'grupo_tema_id' => ['required', 'uuid', 'exists:topic_groups,id'],
            'numero_referencia' => ['required']
        ]);
    }

    public function registerTopic ($nameTopic, $topicGroupID): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        return Topic::query()->create([
            "name" => $nameTopic,
            "topic_group_id" => $topicGroupID,
        ]);
    }

    public function updateTotalFailedRecords ($count): void {
        $importProcess = ImportProcess::query()->find($this->importProcessRecord->id);
        $importProcess->total_number_failed_records = (int) $importProcess->total_number_failed_records + (int) $count;
        $importProcess->save();
    }
    public function updateTotalSuccessfulRecords ($count): void {
        $importProcess = ImportProcess::query()->find($this->importProcessRecord->id);
        $importProcess->total_number_successful_records = (int) $importProcess->total_number_failed_records + (int) $count;
        $importProcess->save();
    }

    public static function afterSheet(AfterSheet $event): void
    {

        $importProcess = ImportProcess::query()->find($event->getConcernable()->importProcessRecord->id);
        $importProcess->total_number_of_records = (int) $importProcess->total_number_of_records + (int) $event->getConcernable()->count_row_current_sheet;
        $importProcess->total_number_successful_records = (int) $importProcess->total_number_successful_records + (int) $event->getConcernable()->count_rows_successfully;
        $importProcess->total_number_failed_records = (int) $importProcess->total_number_failed_records + (int) $event->getConcernable()->count_rows_failed;
        $importProcess->status_process_file = "pending";
        $importProcess->save();

        //broadcast(new HelloEvent([]));
        //broadcast(new ImportZonesEvent($event->getConcernable()->failuresArray, $event->getConcernable()->userAuth, $event->getConcernable()->failedBoolean));
    }

    public static function afterImport (AfterImport $event): void {
        $importProcess = ImportProcess::query()->find($event->getConcernable()->importProcessRecord->id);
        $importProcess->status_process_file = "complete";
        $importProcess->save();

        $user = User::query()->find($event->getConcernable()->userAuth->id);

        $user?->notify(new ImportProcessFileFinishedNotification([
            "import-processes-id" => $event->getConcernable()->importProcessRecord->id
        ]));


    }
}
