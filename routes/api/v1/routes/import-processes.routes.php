<?php

use App\Http\Controllers\Api\v1\ImportProcessesController;
use Illuminate\Support\Facades\Route;


// Rutas del recurso ImportProcess

Route::get('imports/import-processes', [ImportProcessesController::class, 'index'])->name('api.v1.import-processes.index');
Route::get('imports/import-processes/{import_process}/relationship/import-records', [ImportProcessesController::class, 'get_relationship_import_records'])->name('api.v1.import-processes.relationship.import_record');
/*
Route::post('import-processes/export', [ImportProcessesController::class, 'export_records'])->name('api.v1.import-processes.export');
Route::post('import-processes/import', [ImportProcessesController::class, 'import_records'])->name('api.v1.import-processes.import');
Route::get('import-processes/import/template', [ImportProcessesController::class, 'download_template_import_records'])->name('api.v1.import-processes.import.template');
*/
