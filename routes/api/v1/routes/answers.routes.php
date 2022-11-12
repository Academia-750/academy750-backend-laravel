<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AnswersController;

// Rutas del recurso Answers

Route::get('answers', [AnswersController::class, 'index'])->name('api.v1.answers.index');
Route::get('answers/{answer}', [AnswersController::class, 'read'])->name('api.v1.answers.read');
Route::post('answers/create', [AnswersController::class, 'create'])->name('api.v1.answers.create');
Route::patch('answers/update/{answer}', [AnswersController::class, 'update'])->name('api.v1.answers.update');
Route::delete('answers/delete/{answer}', [AnswersController::class, 'delete'])->name('api.v1.answers.soft-delete');
Route::post('answers/actions-on-multiple-records', [AnswersController::class, 'action_for_multiple_records'])->name('api.v1.answers.actions-on-multiple-records');
/*
Route::post('answers/export', [AnswersController::class, 'export_records'])->name('api.v1.answers.export');
Route::post('answers/import', [AnswersController::class, 'import_records'])->name('api.v1.answers.import');
Route::get('answers/import/template', [AnswersController::class, 'download_template_import_records'])->name('api.v1.answers.import.template');
*/
