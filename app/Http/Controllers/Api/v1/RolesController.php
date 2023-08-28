<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Roles\CreateRoleRequest;
use App\Http\Requests\Api\v1\Roles\EditRoleRequest;
use App\Http\Requests\Api\v1\Roles\ListPermissionRequest;
use App\Http\Requests\Api\v1\Roles\ListRoleRequest;
use App\Http\Requests\Api\v1\Roles\PermissionRoleRequest;
use App\Http\Resources\Api\Role\v1\RoleItemResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @group Roles
 *
 * APIs for managing user roles and permissions
 */
class RolesController extends Controller
{
    /**
     * Roles: Create
     *
     * Create a new role
     * @authenticated
     * @apiResource App\Http\Resources\Api\Role\v1\RoleItemResource
     * @apiResourceModel App\Models\Role
     */
    public function postCreateRole(CreateRoleRequest $request)
    {

        try {
            $role = Role::create([
                'name' => Role::parseName($request->get('name')),
                'alias_name' => $request->get('name'),
            ]);

            return RoleItemResource::make($role)->response()->setStatusCode(200);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Roles: Edit
     *
     * Update the role name and if is default role or not
     * @authenticated
     * @urlParam roleId string required Role uuid. Example: "uuid"
     * @response status=404 scenario="Role not found"
     * @response status=403 scenario="Role is protected"
     * @apiResource App\Http\Resources\Api\Role\v1\RoleItemResource
     * @apiResourceModel App\Models\Role
     */
    public function putEditRole(EditRoleRequest $request, string $roleId)
    {
        try {

            $role = Role::query()->find($roleId);
            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Role not found'
                ], 404);
            }

            if ($role->protected) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Protected Roles can not be edited'
                ], 403);
            }


            if ($request->get('default_role') === true) {
                Role::where('default_role', true)->update(['default_role' => false]);
            }



            $data = removeNull([
                'alias_name' => $request->get('name'),
                'name' => $request->get('name') ? Role::parseName($request->get('name')) : null,
                'default_role' => $request->get('default_role') ? 1 : null,
            ]);


            Role::query()->find($role->id)->update($data);
            $updated = Role::query()->find($roleId);
            return RoleItemResource::make($updated)->response()->setStatusCode(200);


        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }


    /**
     * Roles: Info
     *
     * Get the information of a single role
     * @urlParam roleId string required Role uuid. Example: "uuid"
     * @response status=404 scenario="Role not found"
     * @response {
     *     "result": [
     *        "id": "uuid",
     *        "name" : "role-name",
     *        "users_count" : 2,
     *        "permissions": [{
     *              "id" : "uuid",
     *              "name": "permission-name",
     *              "alias-name": "Permission Name"
     *        }],
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     */
    public function getRoleInfo(Request $request, string $roleId)
    {
        try {

            $role = Role::with('permissions')
                ->find($roleId);

            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Role not found'
                ], 404);
            }

            // withCount('users') has a documented bug
            $role->users_count = DB::table('model_has_roles')->where('role_id', $role->id)->where(
                'model_type',
                'App\Models\User'
            )->count();

            return RoleItemResource::make($role)->response()->setStatusCode(200);

        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Roles: List
     *
     * Return a paginated list of roles
     * @authenticated
     * @response {
     *     "results": [
     *        "id": "uuid",
     *        "name" : "role-name" ,
     *        "users_count" : 2,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getRolesList(ListRoleRequest $request)
    {
        try {
            $conditions = [
                parseFilter(['name'], $request->get('content'), 'or_like')
            ];


            $query = filterToQuery(Role::query(), $conditions);

            $results = (clone $query)
                ->select('roles.*')
                // Count Role Users
                ->selectSub(function ($query) {
                    $query->from('model_has_roles')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('model_has_roles.role_id', 'roles.id')
                        ->whereRaw('model_has_roles.model_type = "App\Models\User"');
                }, 'users_count')
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
     * Roles: Delete
     *
     * Delete a role. All the users on this role will be assigned to the default role.
     * @authenticated
     * @urlParam roleId string required Role uuid. Example: "uuid"
     * @response status=404 scenario="Role not found"
     * @response status=403 scenario="Role is protected"
     * @response status=409 scenario="Default role can not be deleted"
     * @response {"message": "successfully"}
     */
    public function deleteRole(Request $request, string $roleId)
    {
        try {

            $role = Role::query()->find($roleId);
            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Role not found'
                ], 404);
            }

            if ($role->protected) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Protected Roles can not be edited'
                ], 403);
            }


            if ($role->default_role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'No role is marked as default role'
                ], 409);
            }

            $role->delete();

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
     * Roles: Add Permission
     *
     * Add a permission to the role
     * @authenticated
     * @urlParam roleId string required Role uuid. Example: "uuid"
     * @response status=404 scenario="Role not found"
     * @response status=403 scenario="Permission not available"
     * @response status=409 scenario="Permission already belongs to this role"
     * @response {"message": "successfully"}
     */
    public function postRolePermission(PermissionRoleRequest $request, string $roleId)
    {
        try {

            $role = Role::query()->find($roleId);
            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Role not found'
                ], 404);
            }

            $permission = Permission::find($request->get('permission_id'));

            if (!$permission) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Permission not available'
                ], 403);
            }

            if ($role->hasPermissionTo($permission->name)) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The permission already belongs to this role'
                ], 409);
            }


            $role->givePermissionTo($permission->name);


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
     * Roles: Delete Permission
     *
     * Delete a permission from a role
     * @authenticated
     * @urlParam roleId string required Role uuid. Example: "uuid"
     * @response status=404 scenario="Role not found"
     * @response status=403 scenario="Permission not available"
     * @response status=409 scenario="Permission doesn't belong to this role"
     * @response {"message": "successfully"}
     */
    public function deleteRolePermission(PermissionRoleRequest $request, string $roleId)
    {
        try {

            $role = Role::query()->find($roleId);
            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Role not found'
                ], 404);
            }

            $permission = Permission::query()->find($request->get('permission_id'));
            if (!$permission) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Permission not available'
                ], 403);
            }

            if (!$role->hasPermissionTo($permission->name)) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'The permission doesn`t belong to this role'
                ], 409);
            }

            $role->revokePermissionTo($permission->name);


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
     * Roles: Permissions
     *
     * Return a paginated list of Permissions
     * @authenticated
     * @response {
     *     "results": [
     *        "id": "uuid",
     *        "name" : "role-name" ,
     *        "category" : "my-category" ,
     *        "name_alias" : 'Role Name' ,
     *        "users_count" : 2,
     *        "created_at" : "Iso Date",
     *        "updated_at" : "Iso Date"
     *      ],
     *       "total": 1
     *  }
     */
    public function getPermissionsList(ListPermissionRequest $request)
    {
        try {
            $conditions = [
                parseFilter(['name', 'alias_name', 'category'], $request->get('content'), 'or_like')
            ];


            $query = filterToQuery(Role::query(), $conditions);

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
                'error' => $err->getMessage()
            ], 500);
        }
    }
}