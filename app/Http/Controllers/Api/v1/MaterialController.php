<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Materials\CreateMaterialRequest;
use App\Http\Requests\Api\v1\Materials\CreateTagRequest;

use App\Http\Requests\Api\v1\Materials\CreateWorkspaceRequest;
use App\Http\Requests\Api\v1\Materials\EditMaterialRequest;
use App\Http\Requests\Api\v1\Materials\ListMaterialRequest;
use App\Http\Requests\Api\v1\Materials\ListTagRequest;
use App\Http\Requests\Api\v1\Materials\ListWorkspaceRequest;
use App\Models\Material;
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
            Log::error($err->getMessage());
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
        try {
            $workspace = Workspace::withCount('materials')->find($workspace_id);

            if (!$workspace) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Workspace not found'
                ], 404);
            }

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


    public function postAddMaterial(CreateMaterialRequest $request, $workspaceId)
    {
        try {
            $workspace = Workspace::find($workspaceId);

            if (!$workspace) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Workspace not found'
                ], 404);
            }

            $material = Material::create([
                'name' => $request->get('name'),
                'type' => $request->get('type'),
                'workspace_id' => $workspace->id
            ]);


            return response()->json([
                'status' => 'successfully',
                'result' => $material
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }


    }
    public function putEditMaterial(EditMaterialRequest $request, $materialId)
    {
        try {
            $material = Material::find($materialId);

            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Material not found'
                ], 404);
            }

            $data = removeNull([
                'name' => $request->get('name'),
                'tags' => $request->get('tags') ? join(',', $request->get('tags')) : null,
                'url' => $request->get('url')
            ]);

            Material::query()->find($material->id)->update($data);
            $updated = Material::query()->find($material->id);


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
    public function deleteMaterial($materialId)
    {

        try {
            $material = Material::find($materialId);

            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Material not found'
                ], 404);
            }

            $material->delete();


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
    public function getMaterialInfo($materialId)
    {
        try {
            $material = Material::with('workspace:id,name')->find($materialId);

            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Material not found'
                ], 404);
            }

            return response()->json([
                'status' => 'successfully',
                'result' => $material
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }
    public function getMaterialList(ListMaterialRequest $request)
    {
        try {
            $conditions = [
                parseFilter('workspace_id', $request->get('workspace')),
                parseFilter('materials.type', $request->get('type')),
                parseFilter(['materials.tags'], $request->get('tags'), 'or_like'),
                parseFilter(['materials.name'], $request->get('content'), 'or_like')
            ];

            $query = Material::query()->join('workspaces', 'workspaces.id', '=', 'materials.workspace_id')->select('materials.*', 'workspaces.name as workspace_name');

            filterToQuery($query, $conditions);

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