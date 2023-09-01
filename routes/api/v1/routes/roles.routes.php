<?php

use App\Http\Controllers\Api\v1\RolesController;

Route::post('role', [RolesController::class, 'postCreateRole'])->middleware('onlyAdmin');
Route::get('role/list', [RolesController::class, 'getRolesList'])->middleware('onlyAdmin');
Route::get('role/permissions', [RolesController::class, 'getPermissionsList'])->middleware('onlyAdmin');

Route::get('role/{roleId}', [RolesController::class, 'getRoleInfo'])->middleware('onlyAdmin');
Route::put('role/{roleId}', [RolesController::class, 'putEditRole'])->middleware('onlyAdmin');
Route::delete('role/{roleId}', [RolesController::class, 'deleteRole'])->middleware('onlyAdmin');

Route::post('role/{roleId}/permission', [RolesController::class, 'postRolePermission'])->middleware('onlyAdmin');
Route::delete('role/{roleId}/permission', [RolesController::class, 'deleteRolePermission'])->middleware('onlyAdmin');