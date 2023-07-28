<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Materials\CreateTagRequest;

use App\Http\Requests\Api\v1\Materials\CreateWorkspaceRequest;
use App\Http\Requests\Api\v1\Materials\ListTagRequest;
use App\Http\Requests\Api\v1\Materials\ListWorkspaceRequest;
use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{

    public function postCreateTag(CreateTagRequest $request)
    {
        $duplicated = Tag::query()->where('name', $request->get('name'))->where('type', 'material')->count();

        if ($duplicated) {
            return response()->json([
                'status' => 'error',
                'result' => 'Tag Already exists'
            ], 409);
        }

        try {
            $tag = Tag::create([
                'name' => $request->get('name'),
                'type' => 'material',
            ]);


            return response()->json([
                'status' => 'successfully',
                'result' => $tag
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function getTagList(ListTagRequest $request)
    {
        try {
            $conditions = removeNull([
                parseFilter('type', 'material'),
                parseFilter(['name'], $request->get('content'), 'or_like')
            ]);


            $query = Tag::query()->where(function ($query) use ($conditions) {
                foreach ($conditions as $condition) {
                    $condition($query);
                }
            });


            $results = (clone $query)
                ->orderBy('name', 'asc')
                ->limit($request->get('limit') ?? 20)
                ->get();


            $total = (clone $query)->count();

            return response()->json([
                'status' => 'successfully',
                'results' => $results,
                'total' => $total
            ]);


        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function postCreateWorkspace(CreateWorkspaceRequest $request)
    {
        try {
            $workspace = Workspace::create([
                'name' => $request->get('name'),
                'type' => 'material', // In the future can be different types of workspace
            ]);


            return response()->json([
                'status' => 'successfully',
                'result' => $workspace
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }
    public function putEditWorkspace(CreateWorkspaceRequest $request, $workspace_id)
    {
        $workspace = Workspace::find($workspace_id);

        if (!$workspace) {
            return response()->json([
                'status' => 'error',
                'error' => 'Workspace not found'
            ], 404);
        }

        try {
            $data = removeNull([
                'name' => $request->get('name')
            ]);

            Workspace::query()->find($workspace->id)->update($data);
            $updated = Workspace::query()->find($workspace->id);

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
    public function deleteWorkspace($workspace_id)
    {
        $workspace = Workspace::find($workspace_id);

        if (!$workspace) {
            return response()->json([
                'status' => 'error',
                'error' => 'Workspace not found'
            ], 404);
        }
        try {

            /**
             * WARNING: This will delete all the files assigned to this workspace
             */
            $workspace->delete();

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
    public function getWorkspaceInfo($workspace_id)
    {
        $workspace = Workspace::withCount('materials')->find($workspace_id);

        if (!$workspace) {
            return response()->json([
                'status' => 'error',
                'error' => 'Workspace not found'
            ], 404);
        }
        try {



            return response()->json([
                'status' => 'successfully',
                'result' => $workspace
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }
    public function getWorkspaceList(ListWorkspaceRequest $request)
    {

        try {
            $conditions = [
                parseFilter(['name'], $request->get('content'), 'or_like')
            ];

            $query = filterToQuery(Workspace::query(), $conditions);

            $results = (clone $query)
                ->withCount('materials')
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


    public function postAddMaterial()
    {
    }
    public function putEditMaterial()
    {
    }
    public function deleteEditMaterial()
    {
    }
    public function getMaterialInfo()
    {
    }
    public function getMaterialList()
    {
    }

}