<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\SubtopicsController;

// Rutas del recurso Subtopics

Route::get('subtopics', [SubtopicsController::class, 'index'])->name('api.v1.subtopics.index');
Route::get('subtopics/{subtopic}', [SubtopicsController::class, 'read'])->name('api.v1.subtopics.read');
Route::post('subtopics/create', [SubtopicsController::class, 'create'])->name('api.v1.subtopics.create');
Route::patch('subtopics/update/{subtopic}', [SubtopicsController::class, 'update'])->name('api.v1.subtopics.update');
Route::delete('subtopics/delete/{subtopic}', [SubtopicsController::class, 'delete'])->name('api.v1.subtopics.soft-delete');
Route::post('subtopics/actions-on-multiple-records', [SubtopicsController::class, 'action_for_multiple_records'])->name('api.v1.subtopics.actions-on-multiple-records');
/*
Route::post('subtopics/export', [SubtopicsController::class, 'export_records'])->name('api.v1.subtopics.export');
Route::post('subtopics/import', [SubtopicsController::class, 'import_records'])->name('api.v1.subtopics.import');
Route::get('subtopics/import/template', [SubtopicsController::class, 'download_template_import_records'])->name('api.v1.subtopics.import.template');
*/
