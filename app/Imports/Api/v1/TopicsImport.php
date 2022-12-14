<?php

namespace App\Imports\Api\v1;

use App\Core\Services\HelpersLaravelImportCSVTrait;
use App\Events\Api\v1\HelloEvent;
use App\Models\ImportProcess;
use App\Models\ImportRecord;
use App\Models\Topic;
use App\Models\TopicGroup;
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

        $this->registerImportProcessHistory( $userAuth, $nameFile, "Importar temas" );
    }

    public function collection(Collection $collection): void {

            $importProcess = ImportProcess::query()->find($this->importProcessRecord->id);


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
                            $this->registerTopic(
                                $row["tema"],
                                TopicGroup::query()->firstWhere('name', '=', $row["grupo_tema"])?->getRouteKey()
                            );
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
                            "tema" => [
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
            'grupo_tema' => ['required', 'string', 'exists:topic_groups,name'],
        ]);
    }

    public function registerTopic ($nameTopic, $topicGroupID): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        return Topic::query()->create([
            "name" => $nameTopic,
            "topic_group_id" => $topicGroupID,
        ]);
    }


    public static function afterSheet(AfterSheet $event): void
    {

        $event->getConcernable()->updateDataImportHistory($event);

        //broadcast(new HelloEvent([]));
        //broadcast(new ImportZonesEvent($event->getConcernable()->failuresArray, $event->getConcernable()->userAuth, $event->getConcernable()->failedBoolean));
    }

    public static function afterImport (AfterImport $event): void {

        $importProcessesRecord = $event->getConcernable()->setStatusCompleteImportHistory($event);

        $user = User::query()->find($event->getConcernable()->userAuth->id);

        $user?->notify(new ImportProcessFileFinishedNotification([
            "import-processes-id" => $event->getConcernable()->importProcessRecord->id,
            "title-notification" => "Importación finalizada - Temas",
            "description" => "Importacion de temas finalizado del archivo {$importProcessesRecord->name_file}"
        ]));
    }
}
