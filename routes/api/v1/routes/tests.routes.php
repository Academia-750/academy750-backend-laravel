<?php

use App\Http\Controllers\Api\v1\QuestionnairesController;
use Illuminate\Support\Facades\Route;


// Rutas del recurso Tests

Route::get('tests/unresolved', [QuestionnairesController::class, 'get_tests_unresolved'])->name('api.v1.tests.unresolved');
Route::get('tests/fetch/unresolved/{test}', [QuestionnairesController::class, 'fetch_unresolved_test'])->name('api.v1.tests.fetch.unresolved');
Route::post('tests/create-a-quiz', [QuestionnairesController::class, 'create_a_quiz'])->name('api.v1.create-a-quiz');
