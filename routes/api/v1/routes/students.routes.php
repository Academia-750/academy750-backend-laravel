<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\StudentsController;

// Rutas del recurso Students

Route::get('students', [StudentsController::class, 'index'])->name('api.v1.students.index');
Route::get('students/{student}', [StudentsController::class, 'read'])->name('api.v1.students.read');
Route::post('students/create', [StudentsController::class, 'create'])->name('api.v1.students.create');
Route::patch('students/update/{student}', [StudentsController::class, 'update'])->name('api.v1.students.update');
Route::delete('students/delete/{student}', [StudentsController::class, 'delete'])->name('api.v1.students.soft-delete');
Route::post('students/mass-selection-action', [StudentsController::class, 'mass_selection_for_action'])->name('api.v1.students.massSelectionForAction');
Route::post('students/export', [StudentsController::class, 'export_records'])->name('api.v1.students.export');
Route::post('students/import', [StudentsController::class, 'import_records'])->name('api.v1.students.import');
Route::get('students/import/template', [StudentsController::class, 'download_template_import_records'])->name('api.v1.students.import.template');

/*Route::get('/students/records/archived', [StudentsController::class, 'get_records_archived'])->name('api.v1.students.archived.get');
Route::get('/students/records/archived/restore/{company}', [StudentsController::class, 'restore_archived'])->name('api.v1.students.archived.restore');
Route::delete('/students/records/archived/force-delete/{company}', [StudentsController::class, 'force_delete_archived'])->name('api.v1.students.archived.force-delete');*/
