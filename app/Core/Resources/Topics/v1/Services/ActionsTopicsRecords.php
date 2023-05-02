<?php

namespace App\Core\Resources\Topics\v1\Services;

use App\Models\Subtopic;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActionsTopicsRecords
{
    public static function deleteRecord ($topic) {
        if ( !($topic instanceof Topic) ) {
            $topic = Topic::query()->find($topic);
        }

        $countTestsOfThisTopic = $topic->tests()->count();

        if ($countTestsOfThisTopic > 0) {
            $topic->is_available = 'no';
            $topic->save();
            $topic->subtopics->each(function ($subtopic) {
                $subtopic->update(['is_available' => 'no']);
            });
        } else {
            self::deleteOppositionsByTopicAndSubtopic($topic);
            self::deleteQuestionsUsedInTestsByTopic($topic);

            $topic->subtopics->each(function ($subtopic) {
                $subtopic = Subtopic::query()->findOrFail($subtopic?->id);
                $subtopic?->questions()->delete();
            });

            if (!$topic->subtopics()->count()) {
                $topic->questions()->delete();
            }


            $topic->subtopics()->delete();
        }

        $topic->delete();

        return $topic;
    }

    public static function detachRelationshipSubtopicOfOppositionByTopic ($topic) {
        $oppositions = $topic->oppositions;

    // Eliminando las relaciones entre el tema y sus oposiciones en la tabla "oppositionables"
        foreach ($oppositions as $opposition) {
            $topic->oppositions()->detach($opposition->id);
        }
    }

    public static function detachRelationshipTopicOfOpposition ($topic) {
        $subtopics = $topic->subtopics;

        // Iterando a través de cada subtema
        foreach ($subtopics as $subtopic) {
            // Obteniendo todas las oposiciones relacionadas con el subtema
            $oppositions = $subtopic->oppositions;

            // Eliminando las relaciones entre el subtema y sus oposiciones en la tabla "oppositionables"
            foreach ($oppositions as $opposition) {
                $subtopic->oppositions()->detach($opposition->id);
            }
        }
    }

    public static function deleteOppositionsByTopicAndSubtopic ($topic) {
        $topicId = $topic->id;

    // Obteniendo los IDs de los subtemas relacionados con el tema
        $subtopicIds = $topic->subtopics->pluck('id');

    // Eliminando las relaciones en la tabla "oppositionables" para el tema y todos sus subtemas en una sola consulta
        DB::table('oppositionables')
            ->where(function ($query) use ($topicId, $subtopicIds) {
                $query->where(function ($query) use ($topicId) {
                    $query->where('oppositionable_type', Topic::class)
                        ->where('oppositionable_id', $topicId);
                })->orWhere(function ($query) use ($subtopicIds) {
                    $query->where('oppositionable_type', Subtopic::class)
                        ->whereIn('oppositionable_id', $subtopicIds);
                });
            })
            ->delete();
    }

    public static function deleteQuestionsUsedInTestsByTopic ($topic) {
        DB::table('questions_used_test')
            ->where('topic_id', $topic->id)
            ->delete();
    }
}
