<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Helpers;
use App\Core\Resources\Users\v1\Interfaces\UsersInterface;
use App\Http\Requests\Api\v1\Groups\CreateGroupRequest;
use App\Http\Requests\Api\v1\Groups\EditGroupRequest;
use App\Http\Requests\Api\v1\Groups\ListGroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    public function postCreateGroup(CreateGroupRequest $request)
    {

        $duplicatedCode = Group::query()->where('code', $request->get('code'))->count();
        if ($duplicatedCode) {
            return response()->json([
                'status' => 'error',
                'result' => 'A group with this color already exists'
            ], 409)->send();
        }

        $duplicatedColor = Group::query()->where('color', $request->get('color'))->count();
        if ($duplicatedColor) {
            return response()->json([
                'status' => 'error',
                'result' => 'A group with this color already exists'
            ], 409)->send();
        }

        try {
            $groupCreated = Group::create([
                'name' => $request->get('name'),
                'color' => $request->get('color'),
                'code' => $request->get('code'),
            ]);


            return response()->json([
                'status' => 'successfully',
                'result' => $groupCreated
            ])->send();
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }

    public function putEditGroup(EditGroupRequest $request, string $groupId)
    {
        try {

            $group = Group::query()->find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404)->send();
            }

            $duplicatedCode = Group::query()->where('code', $request->get('code'))->where('id', '!=', $group->id)->count();

            if ($duplicatedCode) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'A group with this color already exists'
                ], 409)->send();
            }

            $duplicatedColor = Group::query()->where('color', $request->get('color'))->where('id', '!=', $group->id)->count();
            if ($duplicatedColor) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'A group with this color already exists'
                ], 409)->send();
            }

            Group::query()->find($group->id)->update($request->all());
            $updated = Group::query()->find($groupId);

            return response()->json([
                'status' => 'successfully',
                'result' => $updated
            ])->send();

        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }


    public function getGroup(Request $request, string $groupId)
    {
        try {

            $group = Group::query()->find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404)->send();
            }



            return response()->json([
                'status' => 'successfully',
                'result' => $group
            ])->send();

        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }

    public function getList(ListGroupRequest $request)
    {
        try {
            $conditions = removeNull([
                parseFilter('color', $request->get('colors'), 'in'),
                parseFilter('code', $request->get('codes'), 'in'),
                parseFilter('name', $request->get('names'), 'in'),
                parseFilter(['name', 'code'], $request->get('content'), 'or_like')
            ]);


            $query = Group::query()->where(function ($query) use ($conditions) {
                foreach ($conditions as $condition) {
                    $condition($query);
                }
            });


            $results = $query
                ->orderBy(defaultValue($request->get('orderBy'), 'created_at'), ($request->get('order') ?? "-1") === "-1" ? 'desc' : 'asc')
                ->offset(defaultValue($request->get('offset'), 0))
                ->limit(defaultValue($request->get('limit'), 20))
                ->get();


            $total = $query->count();


            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => $total
            ])->send();


        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }
    public function deleteGroup(Request $request, string $groupId)
    {
        try {

            $group = Group::query()->find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Group not found'
                ], 404)->send();
            }


            // TODO: Check if we have students enabled

            $group->delete();

            return response()->json([
                'status' => 'successfully'
            ])->send();

        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500)->send();
        }
    }


}