<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Groups\JoinGroupRequest;
use App\Http\Requests\Api\v1\Groups\ListGroupUserRequest;
use App\Models\Group;
use App\Models\GroupUsers;
use Illuminate\Foundation\Auth\User;


class GroupUsersController extends Controller
{
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

            return response()->json([
                'status' => 'successfully',
                'result' => $member
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }

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

            return response()->json([
                'status' => 'successfully',
                'result' => $member
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function list(ListGroupUserRequest $request, string $groupId)
    {

        try {
            $conditions = removeNull([
                parseFilter('group_id', $groupId),
                parseFilter('discharged_at', is_null($request->get('discharged')) ? true : !$request->get('discharged'), 'isNull'),
                parseFilter([
                    'users.full_name',
                    'users.dni',
                    'groups.name'
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
            dump($err);
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }

}