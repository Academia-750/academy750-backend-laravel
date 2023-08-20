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

    public function __construct($userAuth, $nameFile)
    {
        $this->registerImportProcessHistory($userAuth, $nameFile, "Importar preguntas");
    }

    public function collection(Collection $collection): void
    {


        foreach ($collection as $row) {
            try {

                $rowArray = $row->toArray();


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

            } catch (\Throwable $e) {
                DB::rollback();


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
    public function validateRow($row): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return QuestionsImportValidation::validateRowValidator($row);
    }

    public function registerQuestion($dataQuestion, $row): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {

        \Log::debug(json_encode($dataQuestion));


        if ((bool) $dataQuestion["its_for_subtopic"]) {


            $subtopic = Subtopic::query()->firstWhere('uuid', '=', $dataQuestion["topic_id"]);

            if (!$subtopic) {
                abort(500, "No se ha encontrado al subtema");
            }

            $question = QuestionsImportService::registerQuestion($subtopic, $dataQuestion, QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && $row['es_test'] === 'si');

            $this->registerAnswersQuestion(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row, $question?->getKey()));

            return $question;
        }


        $topic = Topic::query()->firstWhere('uuid', '=', $dataQuestion["topic_id"]);

        if (!$topic) {
            abort(500, "No se ha encontrado al tema");
        }

        $question = QuestionsImportService::registerQuestion($topic, $dataQuestion, QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && $row['es_test'] === 'si');

        $this->registerAnswersQuestion(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row, $question?->getKey()));

        return $topic;
    }

    public function registerAnswersQuestion($dataAnswers): void
    {

        QuestionsImportService::registerAnswersOfQuestion($dataAnswers);
    }

    public static function afterSheet(AfterSheet $event): void
    {
        $event->getConcernable()->updateDataImportHistory($event);
    }

    public static function afterImport(AfterImport $event): void
    {


        $importProcessesRecord = $event->getConcernable()->setStatusCompleteImportHistory($event);

        $user = User::query()->findOrFail($event->getConcernable()->userAuth->id);

        $user?->notify(new ImportProcessFileFinishedNotification([
            "import-processes-id" => $event->getConcernable()->importProcessRecord->id,
            "title-notification" => "Importación finalizada - Preguntas",
            "description" => "Importacion de preguntas finalizado del archivo {$importProcessesRecord->name_file}"
        ]));
    }

    public static function importFailed(ImportFailed $event)
    {
        // \Log::debug("Ha ocurrido un error en la importación usando el ImportFailed Event");
    }

    public function onFailure(Failure ...$failures)
    {
        // \Log::debug("Ha ocurrido un error en la importación usando el onFailure");
    }
}