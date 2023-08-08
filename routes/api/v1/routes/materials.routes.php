<?php
use App\Http\Controllers\Api\v1\MaterialController;



Route::post('workspace', [MaterialController::class, 'postCreateWorkspace'])->middleware('onlyAdmin');
Route::get('workspace/list', [MaterialController::class, 'getWorkspaceList'])->middleware('onlyAdmin');
Route::put('workspace/{workspaceId}', [MaterialController::class, 'putEditWorkspace'])->middleware('onlyAdmin');
Route::delete('workspace/{workspaceId}', [MaterialController::class, 'deleteWorkspace'])->middleware('onlyAdmin');
Route::get('workspace/{workspaceId}/info', [MaterialController::class, 'getWorkspaceInfo'])->middleware('onlyAdmin');


Route::post('material/tag', [MaterialController::class, 'postCreateTag'])->middleware('onlyAdmin');
Route::get('material/tag', [MaterialController::class, 'getTagList']);

Route::post('workspace/{workspaceId}/add', [MaterialController::class, 'postAddMaterial'])->middleware('onlyAdmin');
Route::put('material/{id}', [MaterialController::class, 'putEditMaterial'])->middleware('onlyAdmin');
Route::get('material/list', [MaterialController::class, 'getMaterialList'])->middleware('onlyAdmin');
Route::delete('material/{id}', [MaterialController::class, 'deleteMaterial'])->middleware('onlyAdmin');
Route::get('material/{id}/info', [MaterialController::class, 'getMaterialInfo'])->middleware('onlyAdmin');