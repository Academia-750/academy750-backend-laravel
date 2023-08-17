<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Lesson extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'start_time',
        'end_time',
        'is_active',
        'is_online',
        'url'
    ];

    public function materials()
    {
        return $this->belongsToMany(Material::class)->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class)->withPivot(['group_name', 'group_id'])->withTimestamps();
    }


    public function getColor()
    {
        $groups = array_count_values($this->students()->pluck('group_id')->toArray());
        $groupsIdOrdered = array_keys($groups);

        if (count($groupsIdOrdered) === 0) {
            return null;
        }

        return Group::find($groupsIdOrdered[0])->color;
    }

    public function groups()
    {
        return $this->students()->wherePivotNotNull('group_id')->select(['group_id', 'group_name'])->get()->unique('group_id')->makeHidden('pivot');
    }

    /**
     * This query gets the groups of a lesson order by most common group and expose the color
     * To be uses in complex queries where we get a list of lessons
     * query()->joinLeft(...Lesson::groupColorSQL())
     **/

    public static function getColorSQL()
    {
        return [
            DB::raw("(
                    SELECT
                        lesson_id,
                        group_id,
                        color,
                        ROW_NUMBER() OVER (PARTITION BY lesson_id ORDER BY COUNT(group_id) DESC) AS row_num
                    FROM
                        lesson_user
                    JOIN
                        `groups` ON `groups`.id = `lesson_user`.group_id
                    GROUP BY
                        color, group_Id, lesson_id
               )  as lesson_group "
            ),
            function ($join) {
                $join->on('lessons.id', '=', 'lesson_group.lesson_id')
                    ->where('lesson_group.row_num', '=', 1);
            }
        ];
    }

}