<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Lesson\ActivateLessonRequest;
use App\Http\Requests\Api\v1\Lesson\CreateLessonRequest;
use App\Http\Requests\Api\v1\Lesson\CalendarLessonRequest;
use App\Http\Requests\Api\v1\Lesson\UpdateLessonRequest;
use App\Http\Resources\Api\Lesson\v1\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;



/**
 * @group Lessons
 *
 * APIs for managing user's Lessons
 */
class LessonsController extends Controller
{

    /**
     * Lesson: Create
     *
     * Create a new Lesson (active is false)
     * @authenticated
     * @apiResource App\Http\Resources\Api\Lesson\v1\LessonResource
     * @apiResourceModel App\Models\Lesson
     */
    public function postCreateLesson(CreateLessonRequest $request)
    {

        try {
            $lessonCreated = Lesson::create([
                'name' => $request->get('name'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'date' => $request->get('date'),
            ]);


            return LessonResource::make($lessonCreated);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }


    /**
     * Lesson: Edit
     *
     * Only lessons which are not active can be edited.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @apiResource App\Http\Resources\Api\Lesson\v1\LessonResource
     * @apiResourceModel App\Models\Lesson
     * @response status=404 scenario="Lesson not found"
     * @response status=403 scenario="Lesson is Active"
     */
    public function putEditLesson(UpdateLessonRequest $request, string $lessonId)
    {
        try {

            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            if ($lesson->is_active) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'You can not modify an active lesson'
                ], 403);
            }

            $data = removeNull([
                'name' => $request->get('name'),
                'date' => $request->get('date'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'description' => $request->get('description'),
                'is_online' => $request->get('is_online'),
                'url' => $request->get('url'),
            ]);

            Lesson::query()->find($lesson->id)->update($data);
            $updated = Lesson::query()->find($lessonId);

            return LessonResource::make($updated);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Lesson: Info
     *
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @apiResource App\Http\Resources\Api\Lesson\v1\LessonResource
     * @apiResourceModel App\Models\Lesson
     * @response status=404 scenario="Lesson not found"
     */
    public function getLesson(string $lessonId)
    {
        try {

            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            return LessonResource::make($lesson);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Lesson: Activate
     *
     * Only active lessons can be edited. Only active lessons are fully visible for the students.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully"}
     * @response status=404 scenario="Lesson not found"
     */
    public function putActivateLesson(ActivateLessonRequest $request, $lessonId)
    {
        try {

            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            Lesson::query()->find($lesson->id)->update(['is_active' => $request->get('active')]);

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
     * Lesson: Calendar
     *
     * Lessons planned between two dates.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {
     *     "results": [
     *        "id": 1,
     *        "name" : "Law Part 2" ,
     *        "date" : "2023-02-03" ,
     *        "start_time" : '10:00' ,
     *        "end_time" : '12:00' ,
     *        "description" : "We will go through the chapter 2 of the book" ,
     *        "is_online" : false ,
     *        "is_active" : false,
     *        "url" : "https://zoom-url.com" ,
     *        "students": 23,
     *        "assistance": 10,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getLessonCalendar(CalendarLessonRequest $request)
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


            $query = filterToQuery(Lesson::query(), $conditions);

            $results = (clone $query)
                ->select('lessons.*')
                // TODO: Count Students that will join the lesson
                // ->selectSub(function ($query) {
                //     $query->from('group_users')
                //         ->selectRaw('COUNT(*)')
                //         ->whereColumn('group_users.group_id', 'groups.id')
                //         ->whereNull('group_users.discharged_at');
                // }, 'student_count')
                // --
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();


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
     * Lesson: Delete
     *
     * Only active lessons can be edited. Only active lessons are fully visible for the students.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully"}
     * @response status=404 scenario="Lesson not found"
     * @response status=403 scenario="Lesson is Active"
     */
    public function deleteLesson(string $lessonId)
    {
        try {

            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404);
            }

            if ($lesson->is_active) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'You can not delete an active lesson'
                ], 403);
            }

            $lesson->delete();

            return response()->json([
                'status' => 'successfully'
            ]);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }


    // public function postLessonStudent(Request $request, string $lessonId)
    // {

    // }
    // public function postLessonGroup(Request $request, string $lessonId)
    // {

    // }
    // public function deleteLessonStudent(Request $request, string $lessonId)
    // {

    // }

    // public function getLessonStudents(Request $request, string $lessonId)
    // {

    // }

    // public function postLessonMaterial(Request $request, string $lessonId)
    // {

    // }

    // public function deleteLessonMaterial(Request $request, string $lessonId)
    // {

    // }

    // public function getLessonMaterials(Request $request, string $lessonId)
    // {

    // }
}