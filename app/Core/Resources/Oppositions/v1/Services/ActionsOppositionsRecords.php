<?php

namespace App\Core\Resources\Oppositions\v1\Services;

use App\Models\Opposition;

class ActionsOppositionsRecords
{
    public static function deleteOpposition ($opposition) {
        if ( !($opposition instanceof Opposition) ) {
            $opposition = Opposition::query()->find($opposition);
        }

        $opposition->delete();

        return $opposition;
    }
}
