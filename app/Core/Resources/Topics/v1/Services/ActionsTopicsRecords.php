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
            $topic = Topic::query()->findOrFail($topic);
        }

        $countTestsOfThisTopic = $topic->tests()->count();

        if ($countTestsOfThisTopic > 0) {
            // Aquí se indica que este tema tiene Tests ya creados

            // A todos los subtemas de ese tema se les cambia el estado a "no disponible"
            $topic->subtopics->each(function ($subtopic) {
                $subtopic->update(['is_available' => 'no']);

                $subtopic->questions->each(function ($question) {
                    $question->update(['is_visible' => 'no']);
                });
            });



            // A todas las preguntas de ese tema se les cambia el estado a "no visible"
            $topic->questions->each(function ($question) {
                $question->update(['is_visible' => 'no']);
            });

            // A este tema se le cambia el estado a "no disponible
            $topic->is_available = 'no';
            $topic->save();

        } else {

            // Se borra la relación con Oposiciones de este tema y de cada uno de sus subtemas
            self::deleteOppositionsByTopicAndSubtopic($topic);

            // A todos los subtemas de ese tema se les elimina las preguntas que tiene
            $topic->subtopics->each(function ($subtopic) {
                $subtopic = Subtopic::query()->findOrFail($subtopic?->getKey());
                $subtopic?->questions()->delete();
            });

            // Si este tema no tiene subtemas, se eliminan las preguntas que tiene
            if (!$topic->subtopics()->count()) {
                $topic->questions()->delete();
            }


            // Se elimina todos los subtemas de este tema
            $topic->subtopics()->delete();

            // Se elimina fisicamente el tema
            $topic->delete();
        }

        // Se eliminan todas las preguntas de used_questions_tests
        self::deleteQuestionsUsedInTestsByTopic($topic->getKey(), 'topic_id');

        return $topic;
    }

    public static function detachRelationshipSubtopicOfOppositionByTopic ($topic) {
        $oppositions = $topic->oppositions;

    // Eliminando las relaciones entre el tema y sus oposiciones en la tabla "oppositionables"
        foreach ($oppositions as $opposition) {
            $topic->oppositions()->detach($opposition->getKey());
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
                $subtopic->oppositions()->detach($opposition->getKey());
            }
        }
    }

    public static function deleteOppositionsByTopicAndSubtopic ($topic) {
        $topicId = $topic->getKey();

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

    public static function deleteQuestionsUsedInTestsByTopic (string $id, string $field) {
        DB::table('questions_used_test')
            ->where($field, $id)
            ->delete();
    }
}
