<?php

namespace App\Core\Resources\Subtopics\v1\Services;

use App\Models\Topic;
use App\Models\User;

class ActionsSubtopicsRecords
{
    public static function deleteRecord ($topic) {
        if ( !($topic instanceof Topic) ) {
            $topic = Topic::query()->find($topic);
        }

        $topic->delete();

        return $topic;
    }
}
