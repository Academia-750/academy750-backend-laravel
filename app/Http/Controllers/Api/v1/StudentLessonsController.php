<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Lesson\CalendarLessonRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonListRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonMaterialListRequest;
use App\Models\Lesson;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;



/**
 * @group Students
 *
 * API's that provide students access to their materials and lessons, which is limited
 * according to the lessons they are connected at
 */
class StudentLessonsController extends Controller
{

    /**
     * Students: Lesson Calendar
     *
     * Calendar of lessons for a student.
     * Required `see-lessons` permission.
     * @authenticated
     * @response {
     *     "results": [
     *        "id": 1,
     *        "name" : "Law Part 2" ,
     *        "date" : "2023-02-03" ,
     *        "start_time" : '10:00' ,
     *        "end_time" : '12:00' ,
     *        "description" : "We will go through the chapter 2 of the book" ,
     *        "is_online" : false ,
     *        "url" : "https://zoom-url.com" ,
     *        "color": "#990033",
     *        "will_join": 0,
     *        "user_id": 1,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getStudentLessonsCalendar(StudentLessonListRequest $request)
    {
        if (Carbon::parse($request->get('to'))->diffInDays($request->get('from')) > 90) {
            return response()->json([
                'status' => 'error',
                'error' => 'You can only query a maximum range of 90 days'
            ], 422);
        }


        try {
            $conditions = [
                parseFilter(
                    'date',
                    ['from' => Carbon::parse($request->get('from'))->startOfDay(), 'to' => Carbon::parse($request->get('to'))->endOfDay()],
                    'between'
                ),
                parseFilter(['name', 'description'], $request->get('content'), 'or_like')
            ];

            $query = filterToQuery($request->user()->lessons(), $conditions);

            $results = (clone $query)
                ->select('lessons.*', 'lesson_group.color', 'lesson_user.will_join', 'lesson_user.user_id')
                ->leftJoin(...Lesson::getColorSQL())
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get()
                ->makeHidden(['pivot']);


            $total = (clone $query)->count();

            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => $total
            ]);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Students: Lesson Materials
     *
     * Materials and recordings from the lessons in which the student is enabled.
     * The type parameter is required
     * Required `see-lessons` permission.
     * Required permission `material-lessons` for type `material` and `recording-lessons` for type `lessons`
     * @authenticated
     * @response {
     *     "results": [
     *        "id": 1,
     *        "name" : "Law Part 2" ,
     *        "date" : "2023-02-03" ,
     *        "start_time" : '10:00' ,
     *        "end_time" : '12:00' ,
     *        "description" : "We will go through the chapter 2 of the book" ,
     *        "is_online" : false ,
     *        "url" : "https://zoom-url.com" ,
     *        "color": "#990033"
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getStudentLessonMaterials(StudentLessonMaterialListRequest $request)
    {
        try {

            // Verify Im authorized to check this lessons. (Early feedback)
            if ($request->get('lessons')) {
                $not_auth = $request->user()->whereDoesntHave('lessons', function ($query) use ($request) {
                    $query->where('lesson_id', $request->get('lessons'));
                })->pluck('id');

                if (count($not_auth) > 0) {
                    return response()->json([
                        'status' => 'error',
                        'error' => "This user doesn\'t has access to filter in the next lessons {$not_auth}"
                    ], 403);
                }
            }

            $conditions = [
                parseFilter('lesson_material.lesson_id', $request->get('lessons'), 'in'),
                parseFilter('type', $request->get('type')),
                parseFilter(['materials.tags'], $request->get('tags'), 'or_like'),
                parseFilter(['materials.name'], $request->get('content'), 'or_like')
            ];

            $query = DB::table('materials')
                ->join('lesson_material', 'lesson_material.material_id', '=', 'materials.id')
                ->join('lessons', 'lesson_material.lesson_id', '=', 'lessons.id')
                ->join('lesson_user', 'lesson_user.lesson_id', '=', 'lesson_material.lesson_id')
                ->where('lesson_user.user_id', $request->user()->id)
                ->where('lessons.is_active', true)
                ->select([
                    'materials.name as name',
                    'materials.type',
                    'materials.tags',
                    'lesson_material.material_id',
                    'lesson_material.created_at as created_at',
                    'lesson_material.updated_at as updated_at'
                ]);

            filterToQuery(
                $query,
                $conditions
            );

            $results = (clone $query)
                ->orderBy($request->get('orderBy') ?? 'updated_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
                ->offset($request->get('offset') ?? 0)
                ->limit($request->get('limit') ?? 20)
                ->get([]);

            $total = (clone $query)->count();


            dump((clone $query)->toSql());

            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => $total
            ]);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }



}