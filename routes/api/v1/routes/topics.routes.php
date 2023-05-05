<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TopicsController;

// Rutas del recurso Topics

Route::get('topics', [TopicsController::class, 'index'])->name('api.v1.topics.index');
Route::post('topics/available/by-generate-test', [TopicsController::class, 'get_topics_available_for_create_test'])->name('api.v1.topics.topics-available-por-create-test');
Route::get('topics/{topic}', [TopicsController::class, 'read'])->name('api.v1.topics.read');
Route::post('topics/create', [TopicsController::class, 'create'])->name('api.v1.topics.create');
Route::patch('topics/update/{topic}', [TopicsController::class, 'update'])->name('api.v1.topics.update');
Route::delete('topics/delete/{topic}', [TopicsController::class, 'delete'])->name('api.v1.topics.soft-delete');
Route::post('topics/actions-on-multiple-records', [TopicsController::class, 'action_for_multiple_records'])->name('api.v1.topics.actions-on-multiple-records');
Route::post('topics/import', [TopicsController::class, 'import_records'])->name('api.v1.topics.import');

Route::get('topics/import/template', [TopicsController::class, 'download_template_import_records'])->name('api.v1.topics.import.template');

Route::get('topics/student/get-worst-topics', [TopicsController::class, 'topics_get_worst_topics_of_student'])->name('api.v1.topics.worsts-in-test.student');
/*
Route::post('topics/export', [TopicsController::class, 'export_records'])->name('api.v1.topics.export');
Route::post('topics/import', [TopicsController::class, 'import_records'])->name('api.v1.topics.import');
Route::get('topics/import/template', [TopicsController::class, 'download_template_import_records'])->name('api.v1.topics.import.template');
*/
