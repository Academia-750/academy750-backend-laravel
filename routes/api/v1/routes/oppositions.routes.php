<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\OppositionsController;

// Rutas del recurso Oppositions

Route::get('oppositions', [OppositionsController::class, 'index'])->name('api.v1.oppositions.index');
Route::get('oppositions/{opposition}', [OppositionsController::class, 'read'])->name('api.v1.oppositions.read');
Route::post('oppositions/create', [OppositionsController::class, 'create'])->name('api.v1.oppositions.create');
Route::patch('oppositions/update/{opposition}', [OppositionsController::class, 'update'])->name('api.v1.oppositions.update');
Route::delete('oppositions/delete/{opposition}', [OppositionsController::class, 'delete'])->name('api.v1.oppositions.soft-delete');
Route::post('oppositions/mass-selection-action', [OppositionsController::class, 'mass_selection_for_action'])->name('api.v1.oppositions.massSelectionForAction');
Route::post('oppositions/export', [OppositionsController::class, 'export_records'])->name('api.v1.oppositions.export');
Route::post('oppositions/import', [OppositionsController::class, 'import_records'])->name('api.v1.oppositions.import');
Route::get('oppositions/import/template', [OppositionsController::class, 'download_template_import_records'])->name('api.v1.oppositions.import.template');

Route::get('/oppositions/records/archived', [OppositionsController::class, 'get_records_archived'])->name('api.v1.oppositions.archived.get');
Route::get('/oppositions/records/archived/restore/{company}', [OppositionsController::class, 'restore_archived'])->name('api.v1.oppositions.archived.restore');
Route::delete('/oppositions/records/archived/force-delete/{company}', [OppositionsController::class, 'force_delete_archived'])->name('api.v1.oppositions.archived.force-delete');
