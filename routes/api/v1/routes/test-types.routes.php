<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TestTypesController;

// Rutas del recurso TestTypes

Route::get('test-types', [TestTypesController::class, 'index'])->name('api.v1.test-types.index');
Route::get('test-types/{test_type}', [TestTypesController::class, 'read'])->name('api.v1.test-types.read');
Route::post('test-types/create', [TestTypesController::class, 'create'])->name('api.v1.test-types.create');
Route::patch('test-types/update/{test_type}', [TestTypesController::class, 'update'])->name('api.v1.test-types.update');
Route::delete('test-types/delete/{test_type}', [TestTypesController::class, 'delete'])->name('api.v1.test-types.soft-delete');
Route::post('test-types/actions-on-multiple-records', [TestTypesController::class, 'action_for_multiple_records'])->name('api.v1.test-types.actions-on-multiple-records');
/*
Route::post('test-types/export', [TestTypesController::class, 'export_records'])->name('api.v1.test-types.export');
Route::post('test-types/import', [TestTypesController::class, 'import_records'])->name('api.v1.test-types.import');
Route::get('test-types/import/template', [TestTypesController::class, 'download_template_import_records'])->name('api.v1.test-types.import.template');
*/
