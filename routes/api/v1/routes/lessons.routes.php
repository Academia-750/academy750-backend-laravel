<?php

use App\Http\Controllers\Api\v1\LessonsController;
use App\Http\Controllers\Api\v1\StudentLessonsController;
use Illuminate\Support\Facades\Route;

/**
 * Lessons
 */
Route::post('lesson', [LessonsController::class, 'postCreateLesson'])->middleware('onlyAdmin');
Route::get('lesson/calendar', [LessonsController::class, 'getLessonCalendar'])->middleware('onlyAdmin');
Route::get('lesson/{lessonId}', [LessonsController::class, 'getLesson']);
Route::put('lesson/{lessonId}', [LessonsController::class, 'putEditLesson'])->middleware('onlyAdmin');
Route::put('lesson/{lessonId}/active', [LessonsController::class, 'putActivateLesson'])->middleware('onlyAdmin');
Route::delete('lesson/{lessonId}', [LessonsController::class, 'deleteLesson'])->middleware('onlyAdmin');

Route::post('lesson/{lessonId}/student', [LessonsController::class, 'postLessonStudent'])->middleware('onlyAdmin');
Route::post('lesson/{lessonId}/group', [LessonsController::class, 'postLessonGroup'])->middleware('onlyAdmin');
Route::delete('lesson/{lessonId}/student', [LessonsController::class, 'deleteLessonStudent'])->middleware('onlyAdmin');
Route::delete('lesson/{lessonId}/group', [LessonsController::class, 'deleteGroupLesson'])->middleware('onlyAdmin');
Route::get('lesson/{lessonId}/students', [LessonsController::class, 'getLessonStudents']);


Route::post('lesson/{lessonId}/material', [LessonsController::class, 'postLessonMaterial'])->middleware('onlyAdmin');
Route::delete('lesson/{lessonId}/material', [LessonsController::class, 'deleteLessonMaterial'])->middleware('onlyAdmin');
Route::get('lesson/{lessonId}/materials', [LessonsController::class, 'getLessonMaterials'])->middleware('onlyAdmin');

/**
 * Student Lessons
 */
Route::get('student-lessons/calendar', [StudentLessonsController::class, 'getStudentLessonsCalendar']);
Route::get('student-lessons/materials', [StudentLessonsController::class, 'getStudentLessonMaterials']);
Route::get('student-lessons/search', [StudentLessonsController::class, 'getStudentLessonSearch']);


Route::put('student-lessons/{lessonId}/join', [StudentLessonsController::class, 'putJoinLesson']);
Route::get('student-lessons/{lessonId}/info', [StudentLessonsController::class, 'getStudentLessonInfo']);
Route::get('student-lessons/{lessonId}/online', [StudentLessonsController::class, 'getOnlineLesson']);
Route::get('student-lessons/{materialId}/download', [StudentLessonsController::class, 'downloadMaterial']);