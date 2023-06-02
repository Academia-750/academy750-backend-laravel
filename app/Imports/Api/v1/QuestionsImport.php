<?php

namespace App\Imports\Api\v1;

use App\Core\Services\HelpersLaravelImportCSVTrait;
use App\Imports\Api\Services\QuestionsImportService;
use App\Imports\Api\Services\QuestionsImportValidation;
use App\Models\Subtopic;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\Api\ImportProcessFileFinishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Validators\Failure;

class QuestionsImport implements ToCollection, WithHeadingRow, ShouldQueue, WithEvents, WithChunkReading, SkipsOnFailure
{
    use Importable, RegistersEventListeners, HelpersLaravelImportCSVTrait;
    public $topics;

    public function __construct($userAuth, $nameFile) {
        //$this->userAuth = $userAuth;

        //$this->topics = Topic::query()->with("subtopics")->get();
        // \Log::debug("Se ha ejecutado el constructor de la clase");
        $this->registerImportProcessHistory( $userAuth, $nameFile, "Importar preguntas" );
    }

    public function collection(Collection $collection): void {

        /*// \Log::debug($collection->toArray());*/
        // \Log::debug("Se ejecutó el metodo collection sin entrar al bucle");
        foreach ($collection as $row) {
            try {

                $rowArray = $row->toArray();
                // \Log::debug("Arreglo del archivo a importar");
                // \Log::debug($rowArray);
                /*// \Log::debug($rowArray);
                // \Log::debug(array_keys($rowArray));
                // \Log::debug("------------");
                // \Log::debug(gettype($rowArray));
                // \Log::debug($row->toArray());*/

                $current_row = $this->getCurrentRow();

                $errors = [];
                $hasErrors = false;

                $validateData = $this->validateRow($rowArray);

                if ($validateData->fails()) {
                    $hasErrors = true;
                    $errors = $validateData->errors();
                }

                DB::beginTransaction();


                if (!$hasErrors) {
                    /*// \Log::debug($row);
                    // \Log::debug(QuestionsImportService::getDataFormattedForRegisterQuestions($row));
                    // \Log::debug(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row));*/

                    $this->registerQuestion(QuestionsImportService::getDataFormattedForRegisterQuestions($rowArray), $rowArray);
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

                usleep(250);

            } catch (\Exception $e) {
                DB::rollback();
                // \Log::debug("Ha fallado el proceso de importación de preguntas");
                // \Log::debug($e);
                // \Log::debug("---------------------------------------------------");

                $this->count_rows_failed++;

                $this->registerImportRecordHistory([
                    "current-row" => $current_row,
                    "has-errors" => true,
                    "errors-validation" => [
                        "pregunta" => [
                            "Ocurrió un error en el proceso.",
                            $e->getMessage()
                        ]
                    ],
                    'import-process-id' => $this->importProcessRecord->id
                ]);

                usleep(250);

                continue;
                //broadcast(new FailedImportEvent($e->getMessage(), $this->userAuth));
                //broadcast(new ImportUsersEvent(array('errors' => [$e->getMessage()]), $this->userAuth));
            } catch (\Throwable $e) {
                DB::rollback();
                // \Log::debug("Ha fallado el proceso de importación de preguntas");
                // \Log::debug($e);
                // \Log::debug("---------------------------------------------------");

                $this->count_rows_failed++;

                $this->registerImportRecordHistory([
                    "current-row" => $current_row,
                    "has-errors" => true,
                    "errors-validation" => [
                        "pregunta" => [
                            "Ocurrió un error en el proceso.",
                            $e->getMessage()
                        ]
                    ],
                    'import-process-id' => $this->importProcessRecord->id
                ]);

                usleep(250);

                continue;
                //broadcast(new FailedImportEvent($e->getMessage(), $this->userAuth));
                //broadcast(new ImportUsersEvent(array('errors' => [$e->getMessage()]), $this->userAuth));
            }

        }
    }


    public function chunkSize(): int
    {
        // \Log::debug("Se ejecutó el metodo que define la cantidad de chunckSize");
        return 1000;
    }

    /*
     * Custom Function
     *
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     * */
    public function validateRow ($row): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return QuestionsImportValidation::validateRowValidator($row);
    }

    public function registerQuestion ($dataQuestion, $row): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {

        // \Log::debug("Se ha ejecutado el registerQuestion");

        if ((bool) $dataQuestion["its_for_subtopic"]) {

            // \Log::debug("Pertenece a subtema");

            $subtopic = Subtopic::query()->firstWhere('id','=', $dataQuestion["topic_id"]);

            if (!$subtopic) {
                abort(500, "No se ha encontrado al subtema");
            }

            $question = QuestionsImportService::registerQuestion($subtopic, $dataQuestion, QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && $row['es_test'] === 'si');

            $this->registerAnswersQuestion(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row, $question?->getKey()));

            // \Log::debug("Termino de registrar la pregunta en subtema");

            return $question;
        }

        // \Log::debug("Pertenece a tema");

        $topic = Topic::query()->firstWhere('id','=',$dataQuestion["topic_id"]);

        if (!$topic) {
            abort(500, "No se ha encontrado al subtema");
        }

        $question = QuestionsImportService::registerQuestion($topic, $dataQuestion, QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && $row['es_test'] === 'si');

        $this->registerAnswersQuestion(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row, $question?->getKey()));

        // \Log::debug("Termino de registrar la pregunta en tema");
        return $topic;
    }

    public function registerAnswersQuestion ($dataAnswers): void {

        // \Log::debug("Se ha ejecutado el registerAnswersQuestion");
        QuestionsImportService::registerAnswersOfQuestion($dataAnswers);
    }

    public static function afterSheet(AfterSheet $event): void
    {
        // \Log::debug("Se ha ejecutado el evento AfterSheet");
        $event->getConcernable()->updateDataImportHistory($event);
    }

    public static function afterImport (AfterImport $event): void {

        // \Log::debug("Se ha ejecutado el evento AfterImport");

        $importProcessesRecord = $event->getConcernable()->setStatusCompleteImportHistory($event);

        $user = User::query()->find($event->getConcernable()->userAuth->id);

        $user?->notify(new ImportProcessFileFinishedNotification([
            "import-processes-id" => $event->getConcernable()->importProcessRecord->id,
            "title-notification" => "Importación finalizada - Preguntas",
            "description" => "Importacion de preguntas finalizado del archivo {$importProcessesRecord->name_file}"
        ]));
    }

    public static function importFailed (ImportFailed $event)
    {
        // \Log::debug("Ha ocurrido un error en la importación usando el ImportFailed Event");
    }

    public function onFailure(Failure ...$failures)
    {
        // \Log::debug("Ha ocurrido un error en la importación usando el onFailure");
    }
}

