<?php

use App\Http\Controllers\Api\v1\GroupController;

// Rutas del recurso Users

Route::post('group', [GroupController::class, 'postCreateGroup'])->middleware('onlyAdmin');
Route::get('group/list', [GroupController::class, 'getList'])->middleware('onlyAdmin');
Route::get('group/colors', [GroupController::class, 'getColorsAvailable'])->middleware('onlyAdmin');
Route::get('group/{id}', [GroupController::class, 'getGroup']);
Route::put('group/{id}', [GroupController::class, 'putEditGroup'])->middleware('onlyAdmin');
Route::delete('group/{id}', [GroupController::class, 'deleteGroup'])->middleware('onlyAdmin');
// TODO:
// Route::post('group/{id}/join', [UsersController::class, 'index'])
// Route::post('group/{id}/leave', [UsersController::class, 'index'])
