<?php

use App\Http\Controllers\Api\v1\LessonsController;

Route::post('lesson', [LessonsController::class, 'postCreateLesson'])->middleware('onlyAdmin');
Route::get('lesson/list', [LessonsController::class, 'getLessonList'])->middleware('onlyAdmin');
Route::get('lesson/{id}', [LessonsController::class, 'getLesson']);
Route::put('lesson/{id}', [LessonsController::class, 'putEditLesson'])->middleware('onlyAdmin');
Route::delete('lesson/{id}', [LessonsController::class, 'deleteLesson'])->middleware('onlyAdmin');

Route::post('lesson/{id}/student', [LessonsController::class, 'postLessonStudent'])->middleware('onlyAdmin');
Route::post('lesson/{id}/group', [LessonsController::class, 'postLessonGroup'])->middleware('onlyAdmin');
Route::get('lesson/{id}/students', [LessonsController::class, 'getLessonStudents'])->middleware('onlyAdmin');
Route::delete('lesson/{id}/student', [LessonsController::class, 'deleteLessonStudent'])->middleware('onlyAdmin');


Route::post('lesson/{id}/material', [LessonsController::class, 'postLessonMaterial'])->middleware('onlyAdmin');
Route::get('lesson/{id}/materials', [LessonsController::class, 'getLessonMaterials'])->middleware('onlyAdmin');
Route::delete('lesson/{id}/material', [LessonsController::class, 'deleteLessonMaterial'])->middleware('onlyAdmin');