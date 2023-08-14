<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Groups\CreateGroupRequest;
use App\Http\Requests\Api\v1\Groups\EditGroupRequest;
use App\Http\Requests\Api\v1\Groups\JoinGroupRequest;
use App\Http\Requests\Api\v1\Groups\ListGroupRequest;
use App\Http\Requests\Api\v1\Groups\ListGroupUserRequest;
use App\Http\Resources\Api\Group\v1\GroupResource;
use App\Http\Resources\Api\Group\v1\GroupUserResource;
use App\Models\Group;
use App\Models\GroupUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;

/**
 * @group Students Group
 *
 * APIs for managing user's groups
 */
class GroupController extends Controller
{
    /**
     * Group: Create
     *
     * Add a new students group
     * @authenticated
     * @response status=409 scenario="Group code or color already on use"
     * @apiResource App\Http\Resources\Api\Group\v1\GroupResource
     * @apiResourceModel App\Models\Group
     */
    public function postCreateGroup(CreateGroupRequest $request)
    {

        $duplicatedCode = Group::query()->where('code', $request->get('code'))->count();
        if ($duplicatedCode) {
            return response()->json([
                'status' => 'error',
                'result' => 'A group with this color already exists'
            ], 409);
        }

        $duplicatedColor = Group::query()->where('color', $request->get('color'))->count();
        if ($duplicatedColor) {
            return response()->json([
                'status' => 'error',
                'result' => 'A group with this color already exists'
            ], 409);
        }

        try {
            $groupCreated = Group::create([
                'name' => $request->get('name'),
                'color' => $request->get('color'),
                'code' => $request->get('code'),
            ]);



            return GroupResource::make($groupCreated)->response()->setStatusCode(200);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Group: Edit
     *
     * Update Groups Values
     * @authenticated
     * @urlParam groupId integer Group ID
     * @response status=409 scenario="Group code or color already on use"
     * @response status=404 scenario="Group not found"
     * @apiResource App\Http\Resources\Api\Group\v1\GroupResource
     * @apiResourceModel App\Models\Group
     */
    public function putEditGroup(EditGroupRequest $request, string $groupId)
    {
        try {

            $group = Group::query()->find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404);
            }

            $duplicatedCode = Group::query()->where('code', $request->get('code'))->where('id', '!=', $group->id)->count();

            if ($duplicatedCode) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'A group with this color already exists'
                ], 409);
            }

            $duplicatedColor = Group::query()->where('color', $request->get('color'))->where('id', '!=', $group->id)->count();
            if ($duplicatedColor) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'A group with this color already exists'
                ], 409);
            }

            $data = removeNull([
                'name' => $request->get('name'),
                'color' => $request->get('color'),
                'code' => $request->get('code')
            ]);

            Group::query()->find($group->id)->update($data);
            $updated = Group::query()->find($groupId);

            return GroupResource::make($updated)->response()->setStatusCode(200);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }


    /**
     * Group: Info
     *
     * Get single group information
     * @urlParam groupId integer Group ID
     * @response status=404 scenario="Group not found"
     * @apiResource App\Http\Resources\Api\Group\v1\GroupResource
     * @apiResourceModel App\Models\Group
     */
    public function getGroup(Request $request, string $groupId)
    {
        try {

            $group = Group::query()->find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404);
            }



            return GroupResource::make($group)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Group: List
     *
     * Return a paginated list of groups
     * @authenticated
     * @response {
     *     "results": [
     *        "id" : "1" ,
     *        "name" : "Group Wednesday" ,
     *        "code" : "BBCCDE" ,
     *        "color" : "#ffff00" ,
     *        "group_users" : "10" ,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     * @apiResourceAdditional total=2
     */
    public function getList(ListGroupRequest $request)
    {
        try {
            $conditions = [
                parseFilter('color', $request->get('colors'), 'in'),
                parseFilter('code', $request->get('codes'), 'in'),
                parseFilter('name', $request->get('names'), 'in'),
                parseFilter(['name', 'code'], $request->get('content'), 'or_like')
            ];


            $query = filterToQuery(Group::query(), $conditions);

            $results = (clone $query)
                ->select('groups.*')
                // Count Active Users
                ->selectSub(function ($query) {
                    $query->from('group_users')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('group_users.group_id', 'groups.id')
                        ->whereNull('group_users.discharged_at');
                }, 'active_users')
                // --
                ->orderBy($request->get('orderBy') ?? 'created_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
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

    /**
     * Group: Delete
     *
     * Delete a group and all the relations of his students
     * @authenticated
     * @urlParam groupId integer Group ID
     * @response status=404 scenario="Group not found"
     * @response {"message": "successfully"}
     */
    public function deleteGroup(Request $request, string $groupId)
    {
        try {

            $group = Group::query()->find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404);
            }

            $group->delete();

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
     * Group: Colors
     *
     * A group can not have duplicated colors, here you can get the available colors
     * @authenticated
     * @response {"message": "successfully" , "results" : ['#ff0000', '#00ff00', '#0000ff']}
     */
    public function getColorsAvailable()
    {
        try {
            $colors = config('data.group_colors');
            $usedColorCodes = Group::query()->select('color')->distinct()->get()->pluck('color')->toArray();

            $list = array_map(function ($color) use ($usedColorCodes) {
                return ['color' => $color, 'used' => in_array($color, $usedColorCodes)];
            }, $colors);

            return response()->json([
                'status' => 'successfully',
                'results' => $list,
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
     * Group: Join
     *
     * A student can join a group (unless he is already an active member)
     * @authenticated
     * @urlParam groupId integer Group ID
     * @response status=404 scenario="Group not found"
     * @response status=404 scenario="User not found"
     * @response status=403 scenario="The user state is disabled"
     * @response status=409 scenario="The user already belongs the group"
     * @apiResource App\Http\Resources\Api\Group\v1\GroupUserResource
     * @apiResourceModel App\Models\GroupUsers
     */
    public function join(JoinGroupRequest $request, string $groupId)
    {
        try {
            $group = Group::find($groupId);

            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Group not found'
                ], 404);
            }

            $user = User::query()->where('uuid', $request->get('user_id'))->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            if ($user->state === 'disable') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Disabled users can not join groups'
                ], 403);
            }

            $userIsActive = GroupUsers::query()->where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->whereNull('discharged_at')
                ->count();

            if ($userIsActive) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The user is already active in this group'
                ], 409);
            }

            $member = GroupUsers::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
            ]);

            return GroupUserResource::make($member)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Group: Leave
     *
     * Students that are active can leave the groups (Will be come discharged)
     * @authenticated
     * @urlParam groupId integer Group ID
     * @response status=404 scenario="Member not found"
     * @response status=409 scenario="Member exists but is already discharged"
     * @apiResource App\Http\Resources\Api\Group\v1\GroupUserResource
     * @apiResourceModel App\Models\GroupUsers
     */

    public function leave(JoinGroupRequest $request, string $groupId)
    {
        try {
            $user = User::query()->where('uuid', $request->get('user_id'))->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $member = GroupUsers::query()->where('group_id', $groupId)
                ->where('user_id', $user->id)
                ->whereNull('discharged_at')
                ->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found in this group'
                ], 404);
            }

            if ($member->discharged_at) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This member was already discharged from the group'
                ], 409);
            }

            $member->discharged_at = now()->milliseconds(0);

            $member->save();

            return GroupUserResource::make($member)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Group: Student List
     *
     * List of students of a group (setting discharged to true will be the historical of old students)
     * @authenticated
     * @urlParam groupId integer Group ID
     * @response {
     *     "results": [
     *        "id": 1,
     *        "group_id" : "2" ,
     *        "user_id" : "33" ,
     *        "discharged_at" : null ,
     *        "dni" : "00000000A" ,
     *        "fullName" : "Full Name" ,
     *        "name" : "Name" ,
     *        "email" : "email@example.com" ,
     *        "phone" : "644 001 002" ,
     *        "uuid" : "string",
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     * @apiResourceAdditional total=2
     */
    public function list(ListGroupUserRequest $request, string $groupId)
    {

        try {
            $conditions = removeNull([
                parseFilter('group_id', $groupId),
                parseFilter('discharged_at', is_null($request->get('discharged')) ? true : !$request->get('discharged'), 'isNull'),
                parseFilter([
                    'users.full_name',
                    'users.dni',
                    'users.email',
                    'users.phone',
                ], $request->get('content'), 'or_like')
            ]);


            $query = GroupUsers::query()->join('users', 'group_users.user_id', '=', 'users.id')
                ->join('groups', 'group_users.group_id', '=', 'groups.id')
                ->select('group_users.*', 'users.dni', 'users.email', 'users.uuid', 'users.phone', 'users.full_name', 'groups.name');

            filterToQuery($query, $conditions);

            $results = (clone $query)
                ->orderBy($request->get('orderBy') ?? 'created_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
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
                'message' => $err->getMessage()
            ], 500);
        }
    }

}