<?php

namespace App\Core\Resources\Oppositions\v1\Services;

use App\Models\Opposition;
use Illuminate\Support\Facades\DB;

class ActionsOppositionsRecords
{
    public static function deleteOpposition ($opposition) {
        if ( !($opposition instanceof Opposition) ) {
            $opposition = Opposition::query()->findOrFail($opposition);
        }
        $countTestsByOpposition = $opposition->tests()->count();

        if ($countTestsByOpposition > 0) {
            /*$opposition->is_available = 'no';
            $opposition->save();*/

            $opposition->update([
                'is_available' => 'no'
            ]);
        } else {
            $opposition->topics()->detach();
            $opposition->subtopics()->detach();
            $opposition->delete();
        }

        DB::table('questions_used_test')
            ->where('opposition_id', $opposition->getKey())
            ->delete();

        return $opposition;
    }
}
