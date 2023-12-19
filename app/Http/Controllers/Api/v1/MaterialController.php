<?php
namespace App\Http\Controllers\Api\v1;

use App\Core\Resources\Storage\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Materials\MaterialCreateRequest;
use App\Http\Requests\Api\v1\Materials\MateriaTagCreateRequest;

use App\Http\Requests\Api\v1\Materials\WorkspaceCreateRequest;
use App\Http\Requests\Api\v1\Materials\MaterialEditRequest;
use App\Http\Requests\Api\v1\Materials\MaterialListRequest;
use App\Http\Requests\Api\v1\Materials\MaterialTagListRequest;
use App\Http\Requests\Api\v1\Materials\WorkspaceListRequest;
use App\Http\Requests\Api\v1\Materials\WorkspaceSearchRequest;
use App\Http\Resources\Api\Material\v1\MaterialResource;
use App\Http\Resources\Api\Material\v1\WorkspaceResource;
use App\Models\Material;
use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @group Materials
 *
 * APIs for managing Materials and workspaces
 */
class MaterialController extends Controller
{


    /**
     * Tag: Create
     *
     * Add A tag with the type `Material`
     * @authenticated
     * @response status=409 scenario="Duplicated Tag"
     * @response {
     *  "result":
     *      {"name": "Fire", "id": 1, "type": "material" }
     * }
     */
    public function postCreateTag(MateriaTagCreateRequest $request)
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

    /**
     * Tag: Delete
     *
     * Delete a tag of type `Material`
     * @authenticated
     * @urlParam tag_id integer required Tag Id
     * @response status=404 scenario="Tag not found"
     * @response status=409 scenario="Only tags type `material` can be deleted within this endpoint"
     * @response {"message": "successfully"}
     */
    public function deleteMaterialTag($tag_id)
    {
        $tag = Tag::find($tag_id);

        if (!$tag) {
            return response()->json([
                'status' => 'error',
                'result' => 'Tag Not found'
            ], 404);
        }

        if ($tag->type !== 'material') {
            return response()->json([
                'status' => 'error',
                'result' => 'This tag type can not be deleted within this endpoint'
            ], 409);
        }

        try {
            $tag->delete();

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
     * Tag: List
     *
     * Get the content of material tags
     * @authenticated

     * @response {
     *  "results": [
     *      {"name": "Fire", "id": 1, "type": "material"},
     *      {"name": "Water", "id": 2, "type": "material"}
     *   ],
     *   "total": 2
     * }
     */
    public function getTagList(MaterialTagListRequest $request)
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

    /**
     * Workspace: Create
     *
     * @authenticated
     * @apiResource App\Http\Resources\Api\Material\v1\WorkspaceResource
     * @apiResourceModel App\Models\Workspace
     */
    public function postCreateWorkspace(WorkspaceCreateRequest $request)
    {
        try {
            $workspace = Workspace::create([
                'name' => $request->get('name'),
                'type' => 'material',
                // In the future can be different types of workspace
            ]);


            return WorkspaceResource::make($workspace)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Workspace: Edit
     *
     * @authenticated
     * @urlParam workspaceId integer required Workspace Id
     * @apiResource App\Http\Resources\Api\Material\v1\WorkspaceResource
     * @apiResourceModel App\Models\Workspace
     * @response status=404 scenario="Workspace Not found"
     */
    public function putEditWorkspace(WorkspaceCreateRequest $request, $workspace_id)
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

            return WorkspaceResource::make($updated)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Workspace: Delete
     *
     * @authenticated
     * @urlParam workspaceId integer required Workspace Id
     * @response {"message": "successfully"}
     * @response status=404 scenario="Workspace Not found"
     */
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
            $result = Workspace::deleteFromStorage($workspace);

            if (isset($result['error'])) {
                return response()->json([
                    'status' => 'error',
                    'error' => $result['error']
                ], 424);
            }

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

    /**
     * Workspace: Info
     *
     * @authenticated
     * @urlParam workspaceId integer required Workspace Id
     * @apiResource App\Http\Resources\Api\Material\v1\WorkspaceResource
     * @apiResourceModel App\Models\Workspace
     * @response status=404 scenario="Workspace Not found"
     */
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

            return WorkspaceResource::make($workspace)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Workspace: List
     *
     * @authenticated
     * @response {
     *     "results": [
     *        "id": 1,
     *        "name" : "Generic" ,
     *        "materials" : "30" ,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getWorkspaceList(WorkspaceListRequest $request)
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

    /**
     * Material: Create / Add
     *
     * @authenticated
     * @urlParam workspaceId integer required Workspace Id
     * @apiResource App\Http\Resources\Api\Material\v1\MaterialResource
     * @apiResourceModel App\Models\Material
     * @response status=404 scenario="Workspace Not found"
     */
    public function postAddMaterial(MaterialCreateRequest $request, $workspaceId)
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


            return MaterialResource::make($material)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }


    }

    /**
     * Material: Update
     *
     * @authenticated
     * @urlParam materialId integer required Material Id
     * @apiResource App\Http\Resources\Api\Material\v1\MaterialResource
     * @apiResourceModel app\Models\Material
     * @response status=404 scenario="Material Not found"
     * @response status=424 scenario="Override URL fail, we can not delete file from the source"
     */
    public function putEditMaterial(MaterialEditRequest $request, $materialId)
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
                'url' => $request->get('url'),
                'watermark' => $request->get('watermark'),

            ]);

            // Overriding URL => Delete old file
            if ($request->get('url') && $request->get('url') !== $material->url) {
                $result = Material::deleteFromStorage($material);

                if (isset($result['error'])) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 'Error deleting file ' . $result['error']
                    ], 424);
                }
            }

            Material::query()->find($material->id)->update($data);
            $updated = Material::query()->find($material->id);


            return MaterialResource::make($updated)->response()->setStatusCode(200);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }

    }

    /**
     * Material: Delete
     *
     * @authenticated
     * @urlParam materialId integer required Material Id
     * @response {"message": "successfully"}
     * @response status=404 scenario="Material Not found"
     * @response status=424 scenario="Delete material fail: we can not delete URL from the source"
     */
    public function deleteMaterial($materialId): JsonResponse
    {

        try {
            $material = Material::find($materialId);

            if (!$material) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Material not found'
                ], 404);
            }


            // Deleting Material URL
            $result = Material::deleteFromStorage($material);

            if (isset($result['error'])) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Error deleting file ' . $result['error']
                ], 424);
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

    /**
     * Material: Info
     *
     * Only for admins, at it will expose the unprotected source URL
     * @authenticated
     * @urlParam materialId integer required Material Id
     * @apiResource App\Http\Resources\Api\Material\v1\MaterialResource
     * @apiResourceModel App\Models\Material
     * @response status=404 scenario="Material Not found"
     */
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

            return MaterialResource::make($material);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Material: List
     *
     * Search materials
     * @authenticated
     * @urlParam materialId integer required Material Id
     * @response {
     *     "results": [
     *        "id": 1,
     *        "workspace_id" : 4 ,
     *        "workspace_name" : "Generics" ,
     *        "type" :"materials",
     *        "name" : "Generic" ,
     *        "url" : "https://my-image.com/2123123123" ,
     *        "tags" : "Fire,Water,Sample" ,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getMaterialList(MaterialListRequest $request)
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



    /**
     * Workspaces: Search
     *
     * Search workspaces
     * This is an Open API and doesn't provide sensitive information
     * @authenticated
     * @response {
     *     "results": [
     *        "id" : "1" ,
     *        "name" : "Workspace Name"
     *      ],
     *  }
     */
    public function getWorkspaceSearch(WorkspaceSearchRequest $request)
    {
        try {
            $conditions = removeNull([
                parseFilter(['name'], $request->get('content'), 'or_like')
            ]);


            $query = Workspace::query()->where(function ($query) use ($conditions) {
                foreach ($conditions as $condition) {
                    $condition($query);
                }
            });

            $results = $query
                ->select('id', 'name')
                ->orderBy('created_at', 'desc')
                ->limit($request->get('limit') ?? 5)
                ->get();


            return response()->json([
                'status' => 'successfully',
                'results' => $results,
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
