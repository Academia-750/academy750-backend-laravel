<?php

namespace App\Core\Resources\Subtopics\v1\Services;

use App\Models\Subtopic;
use App\Models\User;

class ActionsSubtopicsRecords
{
    public static function deleteRecord ($subtopic) {
        if ( !($subtopic instanceof Subtopic) ) {
            $subtopic = Subtopic::query()->find($subtopic);
        }

        $subtopic->delete();

        return $subtopic;
    }
}
