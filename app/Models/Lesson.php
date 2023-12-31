<?php

namespace App\Models;

use App\Notifications\Api\NewLessonAvailable;
use App\Notifications\Api\NewMaterialAvailable;
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

    protected $attributes = [
        'is_online' => false,
        'is_active' => false,
        'description' => '',
        'url' => ''
    ];

    public function materials()
    {
        return $this->belongsToMany(Material::class)->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class)->withPivot(['group_name', 'group_id', 'will_join'])->withTimestamps();
    }


    public function getColor()
    {
        $groups = array_count_values($this->students()->wherePivotNotNull('group_id')->pluck('group_id')->toArray());
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

    /**
     *  Select the users of the group that are not discharged and doesnt exist already on the lesson
     * @return  integer[] List of added students ids
     */
    public function syncGroup($group)
    {



        // Will delete any student that doesnt belong to the group anymore. Add the current active ones
        DB::table('lesson_user')
            ->join('group_users', function ($join) use ($group) {
                $join->on('group_users.group_id', '=', 'lesson_user.group_id');
                $join->on('group_users.user_id', '=', 'lesson_user.user_id');
                $join->whereRaw('`group_users`.`discharged_at` IS NOT NULL');
                $join->whereRaw("NOT EXISTS (
                    SELECT `group2`.`user_id` FROM `group_users` `group2`
                    WHERE `group2`.`group_id` = ?
                    AND  `group2`.`user_id` = `lesson_user`.`user_id`
                    AND `group2`.`discharged_at` IS NULL)",
                    [$group->id]
                );
            })
            ->where('lesson_user.lesson_id', $this->id)
            ->where('lesson_user.group_id', $group->id)
            ->delete();



        $studentsIds = $group->users()
            ->whereNull('discharged_at')
            ->whereNotExists(
                function ($query) {
                    $query->select('lesson_user.id')
                        ->from('lesson_user')
                        ->where('lesson_user.lesson_id', $this->id)
                        ->whereRaw('`lesson_user`.`user_id` = `group_users`.`user_id`');
                }
            )->pluck('user_id');

        $this->students()->attach($studentsIds, ['group_id' => $group->id, 'group_name' => $group->name]);

        return $this->students()->where('group_id', $group->id)->count();
    }

    public function notifyUsers()
    {

        $students = $this->students()->get();
        $lesson = Lesson::find($this->id); // Get a fresh copy of the lesson

        foreach ($students as $student) {
            $student->notify(
                new NewLessonAvailable(
                    $lesson
                )
            );
        }
    }

    public function notifyNewMaterial(Material $material)
    {

        $students = $this->students()->get();
        $lesson = Lesson::find($this->id); // Get a fresh copy of the lesson

        foreach ($students as $student) {
            $student->notify(
                new NewMaterialAvailable(
                    $lesson,
                    $material
                )
            );
        }
    }

}
