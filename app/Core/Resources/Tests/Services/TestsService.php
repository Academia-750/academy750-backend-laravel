<?php

namespace App\Core\Resources\Tests\Services;

use App\Core\Services\UuidGeneratorService;
use App\Models\Opposition;
use App\Models\Test;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestsService
{
    public static function getDataToCreateTests ( $requestCreateTest ): array
    {
        $IDOfTheTopicsSelectedForTheTest = array_map(function ($__topic_uuid) {
            return Topic::query()->firstWhere('uuid', $__topic_uuid)?->getKey();
        }, $requestCreateTest->get('topics_id'));

        $oppositionIdToFindTheTopicsForTheTest = Opposition::query()
            ->firstWhere('uuid', $requestCreateTest->get('opposition_id'))
            ?->getKey();

        $userAuth = Auth::user();

        // DATA
        return [
            'userAuth' => $userAuth,
            'testType' => $requestCreateTest->get('test_type'),
            'topics_id' => $IDOfTheTopicsSelectedForTheTest,
            'opposition_id' => $oppositionIdToFindTheTopicsForTheTest,
            'userAuthID' => $userAuth?->getKey(),
            'CountQuestionsTest' => (int) $requestCreateTest->get('count_questions_for_test'),
            'RequestTestIsCardMemory' =>  $requestCreateTest->get('test_type') === 'card_memory'
        ];
    }

    public static function createTestReference ( array $data ): array
    {

        try {
                 $questionnaireReference = Test::query()->create([
                    "number_of_questions_requested" => $data['CountQuestionsTest'],
                    "number_of_questions_generated" => $data['CountQuestionsTest'], // Se actualizará una vez se obtenga el numero real de preguntas disponibles
                    "test_result" => "0",
                    "is_solved_test" => 'no',
                    'test_type' => $data["testType"],
                    'opposition_id' => $data['opposition_id'],
                    'user_id' => $data['userAuthID'],
                ]);

                $data['testRecordReferenceCreated'] = $questionnaireReference;

                return $data;
        } catch (\Exception $e) {
            abort(500, "Error Crear la cabecera del Test -> File: {$e->getFile()} -> Line: {$e->getLine()} -> Code: {$e->getCode()} -> Trace: {$e->getTraceAsString()} -> Message: {$e->getMessage()}");
        }
    }

    public static function getSubtopicsByOppositionAndTopics (array $topicsSelected_id, int $opposition_id ): array {
        $subtopics_id = DB::select(
            "call get_subtopics_ids_for_test_procedure(?,?)",
            array(
                $opposition_id,
                implode(',', $topicsSelected_id)
            )
        );

        return array_map(function($item) {
            $itemCasted = (array) $item;
            return $itemCasted['oppositionable_id'];
        }, $subtopics_id);
    }

    public static function registerTopicsAndSubtopicsByTest ( array $data ): void
    {
        try {
            $subtopicsEveryTopicAndOpposition = self::getSubtopicsByOppositionAndTopics($data['topics_id'], $data['opposition_id']);

            $data['testRecordReferenceCreated']->topics()->sync($data['topics_id']);
            $data['testRecordReferenceCreated']->subtopics()->sync($subtopicsEveryTopicAndOpposition);

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
