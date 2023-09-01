<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Lesson\ActivateLessonRequest;
use App\Http\Requests\Api\v1\Lesson\AddGroupToLessonRequest;
use App\Http\Requests\Api\v1\Lesson\AddStudentToLessonRequest;
use App\Http\Requests\Api\v1\Lesson\CreateLessonRequest;
use App\Http\Requests\Api\v1\Lesson\CalendarLessonRequest;
use App\Http\Requests\Api\v1\Lesson\ListLessonStudentsRequest;
use App\Http\Requests\Api\v1\Lesson\ListMaterialLessonRequest;
use App\Http\Requests\Api\v1\Lesson\MaterialToLessonRequest;
use App\Http\Requests\Api\v1\Lesson\UpdateLessonRequest;
use App\Http\Resources\Api\Lesson\v1\LessonCollection;
use App\Http\Resources\Api\Lesson\v1\LessonResource;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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


            return LessonResource::make($lessonCreated)->response()->setStatusCode(200);

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

            return LessonResource::make($updated)->response()->setStatusCode(200);

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
     * @response {
     *     "result": [
     *        "id": 1,
     *        "name" : "Law Part 2" ,
     *        "date" : "2023-02-03" ,
     *        "start_time" : '10:00' ,
     *        "end_time" : '12:00' ,
     *        "description" : "We will go through the chapter 2 of the book" ,
     *        "is_online" : false ,
     *        "is_active" : false,
     *        "url" : "https://zoom-url.com" ,
     *        "student_count": 23,
     *        "color": "#990033"
     *        "groups": [
     *               { "group_id": 1, "group_name" : "Group A" },
     *               { "group_id": 2, "group_name" : "Group B" }
     *        ],
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *  }
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

            $lesson->color = $lesson->getColor();
            $lesson->student_count = $lesson->students()->count();
            $lesson->groups = $lesson->groups();

            return LessonResource::make($lesson)->response()->setStatusCode(200);


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

            // If ACTIVE we resync all the active groups
            if ($request->get('active')) {
                foreach ($lesson->groups() as $groupData) {
                    $lesson->syncGroup(Group::find($groupData->group_id));
                }
            }

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
     *        "student_count": 23,
     *        "color": "#990033"
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
                ->select('lessons.*', 'lesson_group.color')
                ->leftJoin(...Lesson::getColorSQL())
                ->selectSub(function ($query) {
                    $query->from('lesson_user')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('lesson_user.lesson_id', 'lessons.id');
                }, 'student_count')
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
                    'result' => 'Lesson not found'
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

    /**
     * Lesson Student: Add
     *
     * Add a single student of a group of students to a lesson.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully"}
     * @response status=404 scenario="Lesson not found"
     * @response status=404 scenario="Student not found"
     * @response status=409 scenario="Student already exists on the group"
     * @response status=403 scenario="User is not a student"
     */
    public function postLessonStudent(AddStudentToLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $user = User::query()->where(['uuid' => $request->get('user_id')])->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'User not found'
                ], 404);
            }

            if (!$user->hasRole('student')) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Only students can join lessons'
                ], 403);
            }

            $exists = $lesson->students()->where(['user_id' => $user->id])->count();


            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The user already belongs to the lesson'
                ], 409);
            }

            $lesson->students()->save($user);

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

    /**
     * Lesson Student: Add Group
     *
     * Sync group of active students to a lesson.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully", "count": 10 }
     * @response status=404 scenario="Lesson not found"
     * @response status=404 scenario="Group not found"
     */
    public function postLessonGroup(AddGroupToLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $group = Group::query()->find($request->get('group_id'));
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404);
            }



            $studentsIds = $lesson->syncGroup($group);


            return response()->json([
                'status' => 'successfully',
                'count' => count($studentsIds)
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
     * Lesson Student: Delete
     *
     * Delete a single student from the group
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully"}
     * @response status=404 scenario="Lesson not found"
     * @response status=409 scenario="Student not in the group"
     */
    public function deleteLessonStudent(AddStudentToLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $student = $lesson->students()->where(['uuid' => $request->get('user_id')])->first();


            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The user is not linked to the lesson'
                ], 409);
            }

            $lesson->students()->detach($student);

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

    /**
     * Lesson Student: Delete Group
     *
     * Delete all the students that were added as part of a group.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully", "count": 3 }
     * @response status=404 scenario="Lesson not found"
     * @response status=409 scenario="Group not found"
     */
    public function deleteGroupLesson(AddGroupToLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $group = Group::query()->find($request->get('group_id'));
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404);
            }

            // Will delete any student that doesnt belong to the group anymore. Add the current active ones
            $result = $lesson->students()->newPivotStatement()->where('group_id', $group->id)->delete();

            return response()->json([
                'status' => 'successfully',
                'count' => $result
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
     * Lesson Student: List
     *
     * Search materials
     * @authenticated
     * @urlParam lessonId integer Lesson Id
     * @response {
     *     "results": [
     *        "user_id": 1,
     *        "user_uuid" : "uuid" ,
     *        "group_name" : "My Group" ,
     *        "group_id" : 3,
     *        "user_dni" : "00000000T" ,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *      "total": 1,
     *      "groups": [
     *             { "group_id": 1, "group_name" : "Group A" },
     *             { "group_id": 2, "group_name" : "Group B" }
     *      ]
     *  }
     * @response status=404 scenario="Lesson not found"
     */
    public function getLessonStudents(ListLessonStudentsRequest $request, $lessonId)
    {
        try {
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $conditions = [
                parseFilter('lesson_id', $lessonId),
                parseFilter(['users.dni', 'users.full_name', 'lesson_user.group_name'], $request->get('content'), 'or_like')
            ];

            $query = DB::query()
                ->from('lesson_user')
                ->select([
                    'lesson_user.user_id',
                    'users.dni',
                    'users.full_name',
                    'users.uuid',
                    'lesson_user.group_id',
                    'lesson_user.group_name',
                    'lesson_user.created_at as created_at',
                    'lesson_user.updated_at as updated_at'
                ])->join('users', 'lesson_user.user_id', '=', 'users.id');


            filterToQuery(
                $query,
                $conditions
            );

            $results = (clone $query)
                ->orderBy($request->get('orderBy') ?? 'lesson_user.updated_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
                ->offset($request->get('offset') ?? 0)
                ->limit($request->get('limit') ?? 20)
                ->get();

            $total = (clone $query)->count();

            $groups = $lesson->groups();


            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => $total,
                'groups' => $groups
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
     * Lesson Material: Add
     *
     * Add a material to the lesson. The materia will be available to the students
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully"}
     * @response status=404 scenario="Lesson not found"
     * @response status=404 scenario="Material not found"
     * @response status=409 scenario="Material already exists on the group"
     */
    public function postLessonMaterial(MaterialToLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::query()->find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $material = Material::find($request->get('material_id'));

            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Material not found'
                ], 404);
            }

            $exists = $lesson->materials()->where(['material_id' => $material->id])->count();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The material already belongs to the lesson'
                ], 409);
            }

            $lesson->materials()->save($material);

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

    /**
     * Lesson Material: Delete
     *
     * Delete a material from the lesson.
     * @authenticated
     * @urlParam lessonId integer required Lesson ID
     * @response {"message": "successfully"}
     * @response status=404 scenario="Lesson not found"
     * @response status=409 scenario="Material is not associated with the lesson"
     */
    public function deleteLessonMaterial(MaterialToLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $material = $lesson->materials()->find($request->get('material_id'));


            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The material is not bind to the lesson'
                ], 409);
            }

            $lesson->materials()->detach($material);

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

    /**
     * Lesson Material: List
     *
     * Search materials
     * @authenticated
     * @urlParam lessonId integer required Lesson Id
     * @response {
     *     "results": [
     *        "material_id": 1,
     *        "type" : "recording" ,
     *        "tags" : "fire,water,smoke",
     *        "user_dni" : "00000000T" ,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     * @response status=404 scenario="Lesson not found"
     */
    public function getLessonMaterials(ListMaterialLessonRequest $request, string $lessonId)
    {
        try {
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Lesson not found'
                ], 404);
            }

            $conditions = [
                parseFilter('lesson_id', $lessonId),
                parseFilter('type', $request->get('type')),
                parseFilter(['materials.tags'], $request->get('tags'), 'or_like'),
                parseFilter(['materials.name'], $request->get('content'), 'or_like')
            ];

            $query = DB::query()
                ->from('lesson_material')
                ->select([
                    'materials.name',
                    'materials.type',
                    'materials.tags',
                    'lesson_material.material_id',
                    'lesson_material.created_at as created_at',
                    'lesson_material.updated_at as updated_at'
                ])->join('materials', 'lesson_material.material_id', '=', 'materials.id');


            filterToQuery(
                $query,
                $conditions
            );

            $results = (clone $query)
                ->orderBy($request->get('orderBy') ?? 'updated_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
                ->offset($request->get('offset') ?? 0)
                ->limit($request->get('limit') ?? 20)
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
}