<?php

namespace App\Imports\Api\v1;

use App\Core\Services\HelpersLaravelImportCSVTrait;
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

                /*\Log::debug("Pregunta: {$row["pregunta"]}");
                \Log::debug("Explicacion: {$row["explicacion_texto"]}");
                \Log::debug("Tema UUID: {$row["tema_uuid"]}");
                \Log::debug("Subtema UUID: {$row["subtema_uuid"]}");

                \Log::debug("Respuesta correcta: {$row["respuesta_correcta"]}");
                \Log::debug("Es agrupadora respuesta correcta: {$row["es_agrupadora_respuesta_correcta"]}");
                \Log::debug("Type respuesta correcta: " . gettype($row["es_agrupadora_respuesta_correcta"]));

                \Log::debug("Respuesta 1: {$row["respuesta_1"]}");
                \Log::debug("Es agrupadora respuesta 1: {$row["es_agrupadora_respuesta_1"]}");
                \Log::debug("Type respuesta 1: " . gettype($row["es_agrupadora_respuesta_1"]));

                \Log::debug("Respuesta 2: {$row["respuesta_2"]}");
                \Log::debug("Es agrupadora respuesta 2: {$row["es_agrupadora_respuesta_2"]}");
                \Log::debug("Type respuesta 2: " . gettype($row["es_agrupadora_respuesta_2"]));

                \Log::debug("Respuesta 3: {$row["respuesta_3"]}");
                \Log::debug("Es agrupadora respuesta 3: {$row["es_agrupadora_respuesta_3"]}");
                \Log::debug("Type respuesta 3: " . gettype($row["es_agrupadora_respuesta_3"]));*/

                DB::beginTransaction();


                if (!$hasErrors) {
                    $this->registerQuestion([
                        "question" => $row["pregunta"],
                        "reason" => $row["explicacion_texto"],
                        "topic_id" => $row["tema_uuid"],
                        "subtopic_id" => $row["subtema_uuid"],
                        "es_test" => $row["es_test"],
                        "es_tarjeta_de_memoria" => $row["es_tarjeta_de_memoria"],
                    ],[
                        "answer-correct" => $row["respuesta_correcta"],
                        "is-grouper-answer-correct" => $row["es_agrupadora_respuesta_correcta"],
                        "answer-1" => $row["respuesta_1"],
                        "is-grouper-answer-one" => $row["es_agrupadora_respuesta_1"],
                        "answer-2" => $row["respuesta_2"],
                        "is-grouper-answer-two" => $row["es_agrupadora_respuesta_2"],
                        "answer-3" => $row["respuesta_3"],
                        "is-grouper-answer-three" => $row["es_agrupadora_respuesta_3"],
                    ]);
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
        return Validator::make($row->toArray(), [
            'tema_uuid' => ['required', 'uuid', 'exists:topics,id'],
            'subtema_uuid' => ['nullable', Rule::when( (bool) $row["subtema_uuid"],
                ['uuid', 'exists:subtopics,id', new SubtopicBelongsTopicRule($row["tema_uuid"], $this->topics)]
            )],
            'pregunta' => ['required','max:255'],
            'es_test' => ['nullable', Rule::when( (bool) $row["es_test"],
                ['in:si,no']
            )],
            'es_tarjeta_de_memoria' => ['nullable', Rule::when( (bool) $row["es_tarjeta_de_memoria"],
                ['in:si,no']
            )],
            "respuesta_correcta" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_correcta' => ['nullable', Rule::when( (bool) $row["es_agrupadora_respuesta_correcta"],
                ['in:si,no']
            )],
            "respuesta_1" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_1' => ['nullable', Rule::when( (bool) $row["es_agrupadora_respuesta_1"],
                ['in:si,no']
            )],
            "respuesta_2" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_2' => ['nullable', Rule::when( (bool) $row["es_agrupadora_respuesta_2"],
                ['in:si,no']
            )],
            "respuesta_3" => [
                'required', 'max:255'
            ],
            'es_agrupadora_respuesta_3' => ['nullable', Rule::when( (bool) $row["es_agrupadora_respuesta_3"],
                ['in:si,no']
            )],
            'explicacion_texto' => ['required', 'max:400']
        ]);
    }

    public function registerQuestion ($dataQuestion, $dataAnswers): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        if ((bool) $dataQuestion["subtopic_id"]) {
            $subtopic = Subtopic::query()->firstWhere('id','=',$dataQuestion["subtopic_id"]);

            $question = $subtopic?->questions()->create([
                'question' => $dataQuestion["question"],
                'reason' => $dataQuestion["reason"],
                'is_visible' => 'yes',
                "its_for_test" => $dataQuestion["es_test"],
                "its_for_card_memory" => $dataQuestion["es_tarjeta_de_memoria"],
            ]);

            $this->registerAnswersQuestion($question->id, $dataAnswers);

            return $question;
        }

        $topic = Topic::query()->firstWhere('id','=',$dataQuestion["topic_id"]);

        $question = $topic?->questions()->create([
            'question' => $dataQuestion["question"],
            'reason' => $dataQuestion["reason"],
            'is_visible' => 'yes',
            'its_for_test' => $dataQuestion[''],
            'its_for_card_memory' => $dataQuestion[''],
        ]);

        $this->registerAnswersQuestion($question->id, $dataAnswers);

        return $topic;
    }

    public function isGrouperAnswer ($answer): string {
        if ($answer === 'si') {
            return 'yes';
        }
        return 'no';
    }

    public function getBoolIsGrouperAnswer ($value): string {
        if ($value) {
            return $this->isGrouperAnswer($value);
        }
        return 'no';
    }

    public function registerAnswersQuestion ($question_id, $dataAnswers): void {

        Answer::query()->create([
            'answer' => $dataAnswers["answer-correct"],
            'is_grouper_answer' => $this->getBoolIsGrouperAnswer($dataAnswers["is-grouper-answer-correct"]),
            'is_correct_answer' => 'yes',
            'question_id' => $question_id
        ]);

        Answer::query()->create([
            'answer' => $dataAnswers["answer-1"],
            'is_grouper_answer' => $this->getBoolIsGrouperAnswer($dataAnswers["is-grouper-answer-one"]),
            'is_correct_answer' => 'no',
            'question_id' => $question_id
        ]);

        Answer::query()->create([
            'answer' => $dataAnswers["answer-2"],
            'is_grouper_answer' => $this->getBoolIsGrouperAnswer($dataAnswers["is-grouper-answer-two"]),
            'is_correct_answer' => 'no',
            'question_id' => $question_id
        ]);

        Answer::query()->create([
            'answer' => $dataAnswers["answer-3"],
            'is_grouper_answer' => $this->getBoolIsGrouperAnswer($dataAnswers["is-grouper-answer-three"]),
            'is_correct_answer' => 'no',
            'question_id' => $question_id
        ]);
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

