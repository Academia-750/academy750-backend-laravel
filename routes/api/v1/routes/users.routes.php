<?php

use App\Http\Controllers\Api\v1\UsersController;
use Illuminate\Support\Facades\Route;

// Rutas del recurso Users

Route::get('users', [UsersController::class, 'index'])->name('api.v1.users.index');
Route::get('users/{user}', [UsersController::class, 'read'])->name('api.v1.users.read');
Route::post('users/create', [UsersController::class, 'create'])->name('api.v1.users.create');
Route::patch('users/update/{user}', [UsersController::class, 'update'])->name('api.v1.users.update');
Route::delete('users/delete/{user}', [UsersController::class, 'delete'])->name('api.v1.users.delete');
Route::post('users/actions-on-multiple-records', [UsersController::class, 'mass_selection_for_action'])->name('api.v1.users.actions-on-multiple-records');
Route::post('users/disable-account/{user}', [UsersController::class, 'disable_account'])->name('api.v1.users.disable-account');
Route::post('users/enable-account/{user}', [UsersController::class, 'enable_account'])->name('api.v1.users.enable-account');
/*Route::post('users/export', [UsersController::class, 'export_records'])->name('api.v1.users.export');
Route::post('users/import', [UsersController::class, 'import_records'])->name('api.v1.users.import');*/
Route::get('users/import/template', [UsersController::class, 'download_template_import_records'])->name('api.v1.users.import.template');

/*Route::get('/students/records/archived', [UsersController::class, 'get_records_archived'])->name('api.v1.students.archived.get');
Route::get('/students/records/archived/restore/{company}', [UsersController::class, 'restore_archived'])->name('api.v1.students.archived.restore');
Route::delete('/students/records/archived/force-delete/{company}', [UsersController::class, 'force_delete_archived'])->name('api.v1.students.archived.force-delete');*/

Route::get('roles/get-data/student', static function () {
    if (!auth()->user()?->hasRole('admin')) {
        abort(404);
    }

    return \App\Http\Resources\Api\Role\v1\RoleResource::make(
        \App\Models\Role::query()->firstWhere('name', '=', 'student')
    );
});


Route::get('guest/user/contact-us', [UsersController::class, 'contactsUS'])->name('api.v1.users.home-page.form.contact-us');
