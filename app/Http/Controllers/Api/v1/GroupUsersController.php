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
                    'result' => 'Group not found'
                ], 404)->send();
            }

            $user = User::find($request->get('user_id'));

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'User not found'
                ], 404)->send();
            }

            $userIsActive = GroupUsers::query()->where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->whereNull('discharged_at')
                ->count();

            if ($userIsActive) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The user is already active in this group'
                ], 409)->send();
            }

            $member = GroupUsers::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'successfully',
                'result' => $member
            ], 200)->send();
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }

    public function leave(JoinGroupRequest $request, string $groupId)
    {
        try {
            $member = GroupUsers::query()->where('group_id', $groupId)
                ->where('user_id', $request->get('user_id'))
                ->whereNull('discharged_at')
                ->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'User not found in this group'
                ], 404)->send();
            }

            if ($member->discharged_at) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'This member was already discharged from the group'
                ], 409)->send();
            }

            $member->discharged_at = now()->milliseconds(0);

            $member->save();

            return response()->json([
                'status' => 'successfully',
                'result' => $member
            ], 200)->send();
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }

    public function list(ListGroupUserRequest $request, string $groupId)
    {
        try {
            $conditions = removeNull([
                parseFilter('group_id', $groupId),
                parseFilter(['users.first_name', 'users.dni', 'groups.name'], $request->get('content'), 'or_like')
            ]);


            $query = GroupUsers::query()->join('users', 'group_users.user_id', '=', 'users.id')
                ->join('groups', 'group_users.group_id', '=', 'groups.id')
                ->select('group_users.*', 'users.dni', 'users.first_name', 'groups.name')
                ->where(function ($query) use ($conditions) {
                    foreach ($conditions as $condition) {
                        $condition($query);
                    }
                });


            $results = $query
                ->orderBy($request->get('orderBy') ?? 'created_at', ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
                ->offset($request->get('offset') ?? 0)
                ->limit($request->get('limit') ?? 20)
                ->get();


            $total = $query->count();

            dump($results[0]->user);


            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => $total
            ])->send();


        } catch (\Exception $err) {
            dump($err);
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }

}