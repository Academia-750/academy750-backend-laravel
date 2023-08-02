<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Lesson\CreateLessonRequest;
use App\Http\Requests\Api\v1\Lesson\ListLessonRequest;
use App\Http\Requests\Api\v1\Lesson\UpdateLessonRequest;
use App\Models\Lesson;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Log;



class LessonsController extends Controller
{

    public function postCreateLesson(CreateLessonRequest $request)
    {

        try {
            $lessonCreated = Lesson::create([
                'name' => $request->get('name'),
                'start_time' => $request->get('start_time'),
                'end_time' => $request->get('end_time'),
                'date' => $request->get('date'),
            ]);


            return response()->json([
                'status' => 'successfully',
                'result' => $lessonCreated
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

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

            return response()->json([
                'status' => 'successfully',
                'result' => $updated
            ]);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }


    public function getLesson(Request $request, string $lessonId)
    {
        try {

            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            return response()->json([
                'status' => 'successfully',
                'result' => $lesson
            ]);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function getLessonList(ListLessonRequest $request)
    {
        if ($request->get('to') - $request->get('from') > 90) {
            return response()->json([
                'status' => 'error',
                'error' => 'You can only query a maximum range of 90 days'
            ], 403);
        }


        try {
            $conditions = [
                parseFilter(
                    'date',
                    ['from' => $request->get('from'), 'to' => $request->get('to')],
                    'between'
                ),
                parseFilter(['name', 'description'], $request->get('content'), 'or_like')
            ];


            $query = filterToQuery(Lesson::query(), $conditions);

            $results = (clone $query)
                ->select('lessons.*')
                // Count Students that will join the lesson
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
    public function deleteLesson(Request $request, string $lessonId)
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


    public function postLessonStudent(Request $request, string $lessonId)
    {

    }
    public function postLessonGroup(Request $request, string $lessonId)
    {

    }
    public function deleteLessonStudent(Request $request, string $lessonId)
    {

    }

    public function getLessonStudents(Request $request, string $lessonId)
    {

    }

    public function postLessonMaterial(Request $request, string $lessonId)
    {

    }

    public function deleteLessonMaterial(Request $request, string $lessonId)
    {

    }

    public function getLessonMaterials(Request $request, string $lessonId)
    {

    }
}