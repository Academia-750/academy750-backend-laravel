<?php

use App\Http\Controllers\Api\v1\QuestionnairesController;
use Illuminate\Support\Facades\Route;


// Rutas del recurso Tests

Route::get('tests/unresolved', [QuestionnairesController::class, 'get_tests_unresolved'])->name('api.v1.tests.unresolved');
Route::get('tests/cards-memory', [QuestionnairesController::class, 'get_cards_memory'])->name('api.v1.tests.cards-memory');
Route::get('tests/fetch/unresolved/{test}', [QuestionnairesController::class, 'fetch_unresolved_test'])->name('api.v1.tests.fetch.unresolved');
Route::get('tests/fetch/card-memory/{test}', [QuestionnairesController::class, 'fetch_card_memory'])->name('api.v1.tests.fetch.card-memory');
Route::post('tests/create-a-quiz', [QuestionnairesController::class, 'create_a_quiz'])->name('api.v1.create-a-quiz');
Route::post('tests/questionnaire/resolve-question', [QuestionnairesController::class, 'resolve_a_question_of_test'])->name('api.v1.resolve-question-of-test');
Route::post('tests/questionnaire/{test}/grade-test', [QuestionnairesController::class, 'grade_a_test'])->name('api.v1.grade-test');
Route::get('tests/fetch/history/test-complete/{test}', [QuestionnairesController::class, 'fetch_test_completed'])->name('api.v1.tests.fetch.complete');
