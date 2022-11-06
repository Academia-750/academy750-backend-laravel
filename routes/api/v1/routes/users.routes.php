<?php

use App\Http\Controllers\Api\v1\UsersController;
use Illuminate\Support\Facades\Route;

// Rutas del recurso Users

Route::get('users', [UsersController::class, 'index'])->name('api.v1.users.index');
Route::get('users/{user}', [UsersController::class, 'read'])->name('api.v1.users.read');
Route::post('users/create', [UsersController::class, 'create'])->name('api.v1.users.create');
Route::patch('users/update/{user}', [UsersController::class, 'update'])->name('api.v1.users.update');
Route::delete('users/delete/{user}', [UsersController::class, 'delete'])->name('api.v1.users.delete');
Route::post('users/mass-selection-action', [UsersController::class, 'mass_selection_for_action'])->name('api.v1.users.massSelectionForAction');
Route::post('users/export', [UsersController::class, 'export_records'])->name('api.v1.users.export');
Route::post('users/import', [UsersController::class, 'import_records'])->name('api.v1.users.import');
Route::get('users/import/template', [UsersController::class, 'download_template_import_records'])->name('api.v1.users.import.template');

/*Route::get('/students/records/archived', [UsersController::class, 'get_records_archived'])->name('api.v1.students.archived.get');
Route::get('/students/records/archived/restore/{company}', [UsersController::class, 'restore_archived'])->name('api.v1.students.archived.restore');
Route::delete('/students/records/archived/force-delete/{company}', [UsersController::class, 'force_delete_archived'])->name('api.v1.students.archived.force-delete');*/
