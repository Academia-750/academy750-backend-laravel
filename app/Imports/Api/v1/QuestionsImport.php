<?php

namespace App\Imports\Api\v1;

use App\Core\Services\HelpersLaravelImportCSVTrait;
use App\Imports\Api\Services\QuestionsImportService;
use App\Imports\Api\Services\QuestionsImportValidation;
use App\Models\Answer;
use App\Models\Subtopic;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\Api\ImportProcessFileFinishedNotification;
use App\Rules\Api\v1\SubtopicBelongsTopicRule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;

class QuestionsImport implements ToCollection, WithHeadingRow, ShouldQueue, WithEvents, WithChunkReading
{
    use Importable, RegistersEventListeners, HelpersLaravelImportCSVTrait;
    public $topics;

    public function __construct($userAuth, $nameFile) {
        //$this->userAuth = $userAuth;

        $this->topics = Topic::query()->with("subtopics")->get();
        $this->registerImportProcessHistory( $userAuth, $nameFile, "Importar preguntas" );
    }

    public function collection(Collection $collection): void {

        /*\Log::debug($collection->toArray());*/
        foreach ($collection as $row) {
            try {

                $rowArray = $row->toArray();
                \Log::debug("Arreglo del archivo a importar");
                \Log::debug($rowArray);
                /*\Log::debug($rowArray);
                \Log::debug(array_keys($rowArray));
                \Log::debug("------------");
                \Log::debug(gettype($rowArray));
                \Log::debug($row->toArray());*/

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
                    /*\Log::debug($row);
                    \Log::debug(QuestionsImportService::getDataFormattedForRegisterQuestions($row));
                    \Log::debug(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row));*/

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

            } catch (\Exception $e) {
                DB::rollback();
                \Log::debug("Ha fallado el proceso de importación de preguntas");
                \Log::debug($e);
                \Log::debug("---------------------------------------------------");

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
        return QuestionsImportValidation::validateRowValidator($row, $this->topics);
    }

    public function registerQuestion ($dataQuestion, $row): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {

        if ((bool) $dataQuestion["subtopic_id"]) {
            $subtopic = Subtopic::query()->firstWhere('id','=', $dataQuestion["subtopic_id"]);

            $question = QuestionsImportService::registerQuestion($subtopic, $dataQuestion, QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && $row['es_test'] === 'si');

            $this->registerAnswersQuestion(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row, $question?->getRouteKey()));

            return $question;
        }

        $topic = Topic::query()->firstWhere('id','=',$dataQuestion["topic_id"]);

        $question = QuestionsImportService::registerQuestion($topic, $dataQuestion, QuestionsImportValidation::IssetRowInDataRows($row, "es_test") && $row['es_test'] === 'si');

        $this->registerAnswersQuestion(QuestionsImportService::getDataFormattedForRegisterAnswersOfQuestion($row, $question?->getRouteKey()));

        return $topic;
    }

    public function registerAnswersQuestion ($dataAnswers): void {

        QuestionsImportService::registerAnswersOfQuestion($dataAnswers);
    }

    public static function afterSheet(AfterSheet $event): void
    {

        $event->getConcernable()->updateDataImportHistory($event);
    }

    public static function afterImport (AfterImport $event): void {

        $importProcessesRecord = $event->getConcernable()->setStatusCompleteImportHistory($event);

        $user = User::query()->find($event->getConcernable()->userAuth->id);

        $user?->notify(new ImportProcessFileFinishedNotification([
            "import-processes-id" => $event->getConcernable()->importProcessRecord->id,
            "title-notification" => "Importación finalizada - Preguntas",
            "description" => "Importacion de preguntas finalizado del archivo {$importProcessesRecord->name_file}"
        ]));
    }
}

