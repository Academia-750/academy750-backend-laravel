<?php

namespace App\Imports\Api\v1;

use App\Core\Services\HelpersLaravelImportCSVTrait;
use App\Models\ImportProcess;
use App\Models\Subtopic;
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
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;

class SubtopicsImport implements ToCollection, WithHeadingRow, ShouldQueue, WithEvents, WithChunkReading
{
    use Importable, RegistersEventListeners, HelpersLaravelImportCSVTrait;

    public function __construct($userAuth, $nameFile) {
        //$this->userAuth = $userAuth;

        $this->registerImportProcessHistory( $userAuth, $nameFile, "Importar subtemas" );
    }

    public function collection(Collection $collection): void {

        foreach ($collection as $row) {

            try {

                $current_row = $this->getCurrentRow();

                $errors = [];
                $hasErrors = false;

                $validateData = $this->validateRow($row);

                if ($validateData->fails()) {
                    $hasErrors = true;
                    $errors = $validateData->errors();
                }

                DB::beginTransaction();

                if (!$hasErrors) {
                    $this->registerSubtopic($row["subtema"], $row["tema_uuid"]);
                    $this->count_rows_successfully++;
                } else {
                    $this->count_rows_failed++;
                }

                $this->registerImportRecordHistory([
                    "current-row" => $current_row,
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
                    "has-errors" => true,
                    "errors-validation" => [
                        "subtema" => [
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
            'subtema' => ['required', 'max:255'],
            'tema_uuid' => ['required', 'uuid', 'exists:topics,id'],
        ]);
    }

    public function registerSubtopic ($nameSubtopic, $topic_id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        return Subtopic::query()->create([
            "name" => $nameSubtopic,
            "topic_id" => $topic_id,
        ]);
    }


    public static function afterSheet(AfterSheet $event): void
    {

        $event->getConcernable()->updateDataImportHistory($event);
    }

    public static function afterImport (AfterImport $event): void {

        $importProcessesRecord = $event->getConcernable()->setStatusCompleteImportHistory($event);

        $user = User::query()->findOrFail($event->getConcernable()->userAuth->id);

        $user?->notify(new ImportProcessFileFinishedNotification([
            "import-processes-id" => $event->getConcernable()->importProcessRecord->id,
            "title-notification" => "Importación finalizada - Subtemas",
            "description" => "Importacion de subtemas finalizado del archivo {$importProcessesRecord->name_file}"
        ]));
    }
}
