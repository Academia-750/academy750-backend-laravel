<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\QuestionsController;

// Rutas del recurso Questions


Route::post('questions/claim/academia', [QuestionsController::class, 'claim_question_mail'])->name('api.v1.questions.claim.mail');
Route::post('questions/import', [QuestionsController::class, 'import_records'])->name('api.v1.questions.import');

Route::get('questions/import/template', [QuestionsController::class, 'download_template_import_records'])->name('api.v1.questions.import.template');

Route::post('questions/set-mode-edit/{question}/tests', [QuestionsController::class, 'set_mode_edit_question'])->name('api.v1.questions.set-edit-mode-question');
Route::post('questions/set-state-visibility-question/{question}/tests', [QuestionsController::class, 'set_state_visibility_question'])->name('api.v1.questions.set-state-visibility-question');
