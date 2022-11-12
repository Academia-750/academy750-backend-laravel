<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\QuestionsController;

// Rutas del recurso Questions

Route::get('questions', [QuestionsController::class, 'index'])->name('api.v1.questions.index');
Route::get('questions/{question}', [QuestionsController::class, 'read'])->name('api.v1.questions.read');
Route::post('questions/create', [QuestionsController::class, 'create'])->name('api.v1.questions.create');
Route::patch('questions/update/{question}', [QuestionsController::class, 'update'])->name('api.v1.questions.update');
Route::delete('questions/delete/{question}', [QuestionsController::class, 'delete'])->name('api.v1.questions.soft-delete');
Route::post('questions/actions-on-multiple-records', [QuestionsController::class, 'action_for_multiple_records'])->name('api.v1.questions.actions-on-multiple-records');
/*
Route::post('questions/export', [QuestionsController::class, 'export_records'])->name('api.v1.questions.export');
Route::post('questions/import', [QuestionsController::class, 'import_records'])->name('api.v1.questions.import');
Route::get('questions/import/template', [QuestionsController::class, 'download_template_import_records'])->name('api.v1.questions.import.template');
*/
