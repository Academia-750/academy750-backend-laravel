<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonInfoRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonSearchRequest;
use App\Models\Permission;
use App\Models\User;
use File;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonJoinRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonListRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonMaterialListRequest;
use App\Http\Requests\Api\v1\StudentLessons\StudentLessonOnlineRequest;
use App\Models\Lesson;
use App\Models\Material;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use \Laravel\Sanctum\PersonalAccessToken;


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
     *        "material_id": 1,
     *        "name" : "Material Law 2" ,
     *        "type" : "material" ,
     *        "tags" : "fire,water" ,
     *        "lesson_name" : 'Advance Lesson' ,
     *        "lesson_id" : 34 ,
     *        "has_url": true,
     *        "workspace": "name",
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
                $not_auth = Lesson::query()
                    ->whereIn('id', $request->get('lessons'))
                    ->whereNotIn(
                        'id',
                        DB::table('lesson_user')

                            ->select('lesson_id')
                            ->whereIn('lesson_id', $request->get('lessons'))
                            ->where('user_id', $request->user()->id)
                    )
                    ->pluck('id');

                if (count($not_auth) > 0) {
                    return response()->json([
                        'status' => 'error',
                        'error' => "This user doesn\'t has access to filter in the next lessons {$not_auth}"
                    ], 403);
                }
            }

            $conditions = [
                parseFilter('lesson_material.lesson_id', $request->get('lessons'), 'in'),
                parseFilter('materials.type', $request->get('type')),
                parseFilter('workspaces.id', $request->get('workspaces'), 'in'),
                parseFilter(['materials.tags'], $request->get('tags'), 'or_like'),
                parseFilter(['materials.name'], $request->get('content'), 'or_like')
            ];

            $query = DB::table('materials')
                ->join('workspaces', 'workspaces.id', '=', 'materials.workspace_id')
                ->join('lesson_material', 'lesson_material.material_id', '=', 'materials.id')
                ->join('lessons', 'lesson_material.lesson_id', '=', 'lessons.id')
                ->join('lesson_user', 'lesson_user.lesson_id', '=', 'lesson_material.lesson_id')
                ->where('lesson_user.user_id', $request->user()->id)
                ->where('lessons.is_active', true)
                ->select([
                    'lesson_material.material_id',
                    'materials.name as name',
                    'materials.type as type',
                    'materials.tags as tags',
                    'workspaces.name as workspace',
                    'materials.created_at as created_at',
                    'materials.updated_at as updated_at'
                ])
                ->selectRaw('CASE WHEN LENGTH(materials.url) > 0 THEN 1 ELSE 0 END AS `has_url` ')
            ;

            // Only first groupBy is required, but production crash if we dont all all the elements to the groupby
            $query
                ->groupBy('lesson_material.material_id')
                ->groupBy('materials.name')
                ->groupBy('materials.type')
                ->groupBy('materials.tags')
                ->groupBy('materials.created_at')
                ->groupBy('materials.updated_at')
                ->groupBy('workspaces.name')
                ->groupBy('materials.url');


            filterToQuery(
                $query,
                $conditions
            );

            $results = (clone $query)
                ->orderBy($request->get('orderBy') ?? 'updated_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
                ->offset($request->get('offset') ?? 0)
                ->limit($request->get('limit') ?? 20)
                ->get();


            $total = (clone $query)->selectRaw('count(*) OVER() as total')->pluck('total');


            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => isset($total[0]) ? $total[0] : 0
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
     * Students: Search lessons
     *
     * Search lessons of the student via name
     * Required `see-lessons` permission.
     * @authenticated
     * @response {
     *     "results": [
     *        "id": 1,
     *        "name" : "Material Law 2" ,
     *        "date" : "material" ,
     *      ]
     *  }
     * @response status=403 scenario="Required `see-lessons` and `material-lessons` OR `recording-lessons` permissions"
     */
    public function getStudentLessonSearch(StudentLessonSearchRequest $request)
    {
        try {
            $conditions = [
                parseFilter('user_id', $request->user()->id),
                parseFilter(['lessons.name'], $request->get('content'), 'or_like')
            ];

            $query = DB::table('lesson_user')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->select([
                    'lessons.name as name',
                    'lessons.id as id',
                    'lessons.date as date',
                ]);

            filterToQuery(
                $query,
                $conditions
            );

            $results = (clone $query)
                ->orderBy('name', 'desc')
                ->limit($request->get('limit') ?? 20)
                ->get();

            return response()->json([
                'status' => 'successfully',
                'results' => $results
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
     * Students: Lesson Info
     *
     * Get the lesson information
     * Required `see-lessons` permission and to have access to this lesson.
     * @authenticated
     * @response {
     *     "result": [
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
     *      ]
     *  }
     * @response status=403 scenario="Required `see-lessons` and `material-lessons` OR `recording-lessons` permissions"
     * @response status=403 scenario="You don't have access to this lesson"
     * @response status=404 scenario="Lesson not found"
     */
    public function getStudentLessonInfo(StudentLessonInfoRequest $request, int $lessonId)
    {
        try {
            $lesson = Lesson::query()->where('id', $lessonId)->first();

            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Lesson Not found'
                ], 404);
            }

            $query = $request->user()->lessons()->where('lessons.id', $lessonId);

            $results = (clone $query)
                ->select('lessons.*', 'lesson_group.color', 'lesson_user.will_join', 'lesson_user.user_id')
                ->leftJoin(...Lesson::getColorSQL())
                ->get()
                // URL is hidden, requires specials permissions for it
                ->makeHidden(['pivot', 'url'])->toArray();


            if (!isset($results[0])) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Not authorized to see this lesson information'
                ], 403);
            }
            return response()->json([
                'status' => 'successfully',
                'result' => $results[0]
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
     *   'url' => 'https://url.com/room-id'
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


    /**
     * Students: Material URL
     *
     * Returns a encrypted wrapped of that represents the real URL, together a token and a cookie.
     * This material can be reveal using this URL (which is calling to the downloadFile function)
     * Required `material-lessons` and `recording-lessons` permission according to the type of material
     * Admin users can use also the API with no permissions required
     *
     * @urlParam lessonId integer required Lesson Id
     * @authenticated
     * @response {
     *   'status' => 'successfully',
     *   'url' => 'https://url.com/download-material'
     * }
     * @response status=404 scenario="Material not found"
     * @response status=403 scenario="Required `material-lessons` OR `recording-lessons` permissions"
     * @response status=409 scenario="Material not available"
     * @response status=424 scenario="Error generating the PDF URL"
     */
    public function getUrl(Request $request, int $materialId)
    {
        try {
            $material = Material::find($materialId);
            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'error' => "Material not found"
                ], 404);
            }


            if (!$material->canDownload($request->user())) {
                return response()->json([
                    'status' => 'error',
                    'error' => "Not permissions to access this types of material"
                ], 403);
            }


            if ($request->user()->hasRole('admin') === false) {

                $available = $request->user()->lessons()
                    ->where('lessons.is_active', true)
                    ->whereHas('materials', function ($query) use ($material) {
                        $query->where('materials.id', $material->id);
                    })
                    ->count();


                if (!$available) {
                    return response()->json([
                        'status' => 'error',
                        'error' => "Material not available"
                    ], 409);
                }
            }


            /** We handle Download errors differently */
            try {
                $url = $material->downloadUrl($request->user());
            } catch (\Exception $err) {
                Log::error($err->getMessage());
                return response()->json([
                    'status' => 'error',
                    'error' => $err->getMessage()
                ], 424);
            }

            // If the download URL is the same as the original
            // They mean they don't need security and can be sent directly
            if ($url === $material->url) {
                return response()
                    ->json([
                        'status' => 'successfully',
                        'url' => $url,
                    ]);
            }

            [$cookie, $url] = Material::secureURL($request, $url);
            return response()
                ->json([
                    'status' => 'successfully',
                    'url' => $url,
                ])
                // The download URL is public. But we use this cookie to verify the ownership
                ->withCookie($cookie);
        } catch (\Exception $err) {

            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Download Resource
     *
     * Download or redirect to a URL.
     * The URL is not exposed to the user and is validate Via Cookies and user tokens
     * This URL receives a token and collects a cookie left in the browser of the user
     * who call getURL.
     *
     * ONLY if the cookie belongs to the user for which the token is generated, it will
     * download or redirect to the URL
     *
     * @urlParam code Token Code
     * @authenticated
     * @response Binary File or a URL redirection
     * @response status=404 scenario="Wrong Token, missed cookie, any is expired, user doesn't match"
     * @response status=424 scenario="Trying to download an external url but failed"
     */
    public function downloadFile(Request $request, string $code)
    {
        try {
            [$url, $error] = Material::unlockSafeURL($request, $code);

            if ($error) {
                // To trick the user we don't say the reason
                return response()->json([
                    'status' => 'error',
                    'error' => $error
                ], 404);
            }

            // Check if the host is the same as your Laravel application's host
            $parsedUrl = parse_url($url);
            $internalUrl = isset($parsedUrl['host']) && $parsedUrl['host'] === $request->getHost();

            if ($internalUrl || app()->environment() === 'testing') {
                return \Response::download(public_path($parsedUrl['path']));
            }

            $filename = \Str::random();
            $tempImage = tempnam(sys_get_temp_dir(), $filename);
            copy($url, $tempImage);

            return response()->download($tempImage, $filename);


        } catch (\Exception $err) {

            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

}
