<?php

use App\Http\Controllers\Api\v1\GroupController;

Route::post('group', [GroupController::class, 'postCreateGroup'])->middleware('onlyAdmin');
Route::get('group/list', [GroupController::class, 'getList'])->middleware('onlyAdmin');
Route::get('group/colors', [GroupController::class, 'getColorsAvailable'])->middleware('onlyAdmin');
Route::get('group/{groupId}', [GroupController::class, 'getGroup']);
Route::put('group/{groupId}', [GroupController::class, 'putEditGroup'])->middleware('onlyAdmin');
Route::delete('group/{groupId}', [GroupController::class, 'deleteGroup'])->middleware('onlyAdmin');

Route::post('group/{groupId}/join', [GroupController::class, 'join'])->middleware('onlyAdmin');
Route::post('group/{groupId}/leave', [GroupController::class, 'leave'])->middleware('onlyAdmin');
Route::get('group/{groupId}/list', [GroupController::class, 'list'])->middleware('onlyAdmin');