<?php

use App\Http\Controllers\Api\v1\QuestionnairesController;
use Illuminate\Support\Facades\Route;


// Rutas del recurso Tests

Route::get('tests', [QuestionnairesController::class, 'index'])->name('api.v1.tests.index');
Route::get('tests/{test}', [QuestionnairesController::class, 'read'])->name('api.v1.tests.read');
Route::post('tests/generate/questions', [QuestionnairesController::class, 'generate'])->name('api.v1.generate-test');
