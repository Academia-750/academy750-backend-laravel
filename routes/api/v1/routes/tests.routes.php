<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TestsController;

// Rutas del recurso Tests

Route::get('tests', [TestsController::class, 'index'])->name('api.v1.tests.index');
Route::get('tests/{test}', [TestsController::class, 'read'])->name('api.v1.tests.read');
Route::post('tests/create', [TestsController::class, 'create'])->name('api.v1.tests.create');
Route::patch('tests/update/{test}', [TestsController::class, 'update'])->name('api.v1.tests.update');
Route::delete('tests/delete/{test}', [TestsController::class, 'delete'])->name('api.v1.tests.soft-delete');
Route::post('tests/actions-on-multiple-records', [TestsController::class, 'action_for_multiple_records'])->name('api.v1.tests.actions-on-multiple-records');
/*
Route::post('tests/export', [TestsController::class, 'export_records'])->name('api.v1.tests.export');
Route::post('tests/import', [TestsController::class, 'import_records'])->name('api.v1.tests.import');
Route::get('tests/import/template', [TestsController::class, 'download_template_import_records'])->name('api.v1.tests.import.template');
*/
