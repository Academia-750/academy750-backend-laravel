<?php

use App\Http\Controllers\Api\v1\GroupController;
use App\Http\Controllers\Api\v1\GroupUsersController;

Route::post('group', [GroupController::class, 'postCreateGroup'])->middleware('onlyAdmin');
Route::get('group/list', [GroupController::class, 'getList'])->middleware('onlyAdmin');
Route::get('group/colors', [GroupController::class, 'getColorsAvailable'])->middleware('onlyAdmin');
Route::get('group/{id}', [GroupController::class, 'getGroup']);
Route::put('group/{id}', [GroupController::class, 'putEditGroup'])->middleware('onlyAdmin');
Route::delete('group/{id}', [GroupController::class, 'deleteGroup'])->middleware('onlyAdmin');

Route::post('group/{id}/join', [GroupUsersController::class, 'join'])->middleware('onlyAdmin');
Route::post('group/{id}/leave', [GroupUsersController::class, 'leave'])->middleware('onlyAdmin');
Route::get('group/{id}/list', [GroupUsersController::class, 'list'])->middleware('onlyAdmin');