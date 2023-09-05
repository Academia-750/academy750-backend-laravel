<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Lesson\CalendarLessonRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonJoinRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonJoinRquest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonListRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonMaterialListRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonOnlineRequest;
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
     *        "color": "#990033",
     *        "will_join": 0,
     *        "user_id": 1,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     * @response status=403 scenario="Required `see-lessons` permissions"
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
                // URL is hidden, requires specials permissions for it
                ->makeHidden(['pivot', 'url']);


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
     * @response status=403 scenario="Required `see-lessons` and `material-lessons` OR `recording-lessons` permissions"
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
     * Students: Join Lesson
     *
     * Allow students to indicate that they can join a lesson
     * The type parameter is required
     * Required `see-lessons` and `join-lessons` permission.
     * @urlParam lessonId integer required Lesson Id
     * @authenticated
     * @response {
     *   'status' => 'successfully'
     * }
     * @response status=404 scenario="Lesson not found"
     * @response status=403 scenario="Required `see-lessons` and `join-lessons` permissions"
     * @response status=403 scenario="Not allowed to join this lesson"
     * @response status=409 scenario="Lesson not active"
     */
    public function putJoinLesson(StudentLessonJoinRequest $request, int $lessonId)
    {
        try {
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'error' => "Lesson not found"
                ], 404);
            }

            if (!$lesson->is_active) {
                return response()->json([
                    'status' => 'error',
                    'error' => "The lesson is not active yet"
                ], 409);
            }


            $student = $lesson->students()->where('user_id', $request->user()->id)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'error' => "Not allowed to join this lesson"
                ], 403);
            }

            $lesson->students()->updateExistingPivot($student->id, ['will_join' => $request->get('join')]);

            return response()->json([
                'status' => 'successfully',
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
     * Students: Online Lesson
     *
     * Allow students to get online lesson url
     * The type parameter is required
     * Required `see-lessons` and `online-lessons` permission.
     * @urlParam lessonId integer required Lesson Id
     * @authenticated
     * @response {
     *   'status' => 'successfully',
     *   'url' => 'https://url.com/room-id
     * }
     * @response status=404 scenario="Lesson not found"
     * @response status=403 scenario="Required `see-lessons` and `online-lessons` permissions"
     * @response status=403 scenario="Not allowed to join this lesson"
     * @response status=409 scenario="Lesson not active"
     * @response status=409 scenario="Lesson has no online url"
     */
    public function getOnlineLesson(StudentLessonOnlineRequest $request, int $lessonId)
    {
        try {
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'error' => "Lesson not found"
                ], 404);
            }

            if (!$lesson->is_active) {
                return response()->json([
                    'status' => 'error',
                    'error' => "The lesson is not active yet"
                ], 409);
            }

            if (!$lesson->is_online) {
                return response()->json([
                    'status' => 'error',
                    'error' => "This lesson has no online information"
                ], 409);
            }


            $student = $lesson->students()->where('user_id', $request->user()->id)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'error' => "Not allowed to get information of this lesson"
                ], 403);
            }


            return response()->json([
                'status' => 'successfully',
                'url' => $lesson->url
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