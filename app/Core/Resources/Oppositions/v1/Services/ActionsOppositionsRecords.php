<?php

namespace App\Core\Resources\Oppositions\v1\Services;

use App\Models\Opposition;
use Illuminate\Support\Facades\DB;

class ActionsOppositionsRecords
{
    public static function deleteOpposition ($opposition) {
        if ( !($opposition instanceof Opposition) ) {
            $opposition = Opposition::query()->find($opposition);
        }

        $countTestsByOpposition = $opposition->tests()->count();

        if ($countTestsByOpposition > 0) {
            /*$opposition->is_available = 'no';
            $opposition->save();*/

            $opposition->update([
                'is_available' => 'no'
            ]);
            \Log::debug("La oposición si tiene Tests creados");
        } else {
            \Log::debug("La oposición se tiene que eliminar por completo");
            $opposition->topics()->detach();
            $opposition->subtopics()->detach();
            $opposition->delete();
        }

        \Log::debug("Se deben borrar los registros de la tabla questions_used_test");
        DB::table('questions_used_test')
            ->where('opposition_id', $opposition->id)
            ->delete();

        return $opposition;
    }
}
