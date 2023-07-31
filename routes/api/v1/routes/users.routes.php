<?php

use App\Http\Controllers\Api\v1\UsersController;
use App\Http\Resources\Api\Role\v1\RoleResource;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

// Rutas del recurso Users

Route::get('users', [UsersController::class, 'index'])->name('api.v1.users.index');
Route::get('users/search', [UsersController::class, 'search'])->middleware('onlyAdmin')->name('api.v1.users.search');
Route::get('users/{user}', [UsersController::class, 'read'])->name('api.v1.users.read');
Route::post('users/create', [UsersController::class, 'create'])->name('api.v1.users.create')->middleware('onlyAdmin');

Route::patch('users/update/{user}', [UsersController::class, 'update'])->name('api.v1.users.update')->middleware('onlyAdmin');
Route::delete('users/delete/{user}', [UsersController::class, 'delete'])->name('api.v1.users.delete')->middleware('onlyAdmin');
Route::post('users/actions-on-multiple-records', [UsersController::class, 'mass_selection_for_action'])->name('api.v1.users.actions-on-multiple-records')->middleware('onlyAdmin');
/*Route::post('users/disable-account/{user}', [UsersController::class, 'disable_account'])->name('api.v1.users.disable-account');
Route::post('users/enable-account/{user}', [UsersController::class, 'enable_account'])->name('api.v1.users.enable-account');*/
/*Route::post('users/export', [UsersController::class, 'export_records'])->name('api.v1.users.export');
Route::post('users/import', [UsersController::class, 'import_records'])->name('api.v1.users.import');*/
//Route::get('users/import/template', [UsersController::class, 'download_template_import_records'])->name('api.v1.users.import.template');

/*Route::get('/students/records/archived', [UsersController::class, 'get_records_archived'])->name('api.v1.students.archived.get');
Route::get('/students/records/archived/restore/{company}', [UsersController::class, 'restore_archived'])->name('api.v1.students.archived.restore');
Route::delete('/students/records/archived/force-delete/{company}', [UsersController::class, 'force_delete_archived'])->name('api.v1.students.archived.force-delete');*/

Route::get('roles/get-data/student', static function () {
    if (!auth()->user()?->hasRole('admin')) {
        abort(404);
    }

    return RoleResource::make(
        Role::query()->firstWhere('name', '=', 'student')
    );
});

Route::get('verify/token/header-authorization', static function () {
    if (!auth()->user()) {
        abort(401);
    }

    return response()->json([
        'message' => 'successfully'
    ], 200);
});

Route::get('/users/student/tests/fetch/between-period-date', [UsersController::class, 'fetch_tests_between_period_date'])->name('api.v1.users.student.tests.fetch-tests-between-period-date');

Route::post('/users/student/tests/fetch/history-statistical-data-graph', [UsersController::class, 'fetch_history_statistical_data_graph_by_student'])->name('api.v1.users.student.tests.fetch-history-statistical-data-graph');
Route::get('/users/student/tests/fetch/history-questions-by-test-relation-type-question', [UsersController::class, 'fetch_history_questions_by_type_and_period'])->name('api.v1.users.student.tests.fetch-history-questions-by-type-and-period');
Route::get('/users/student/tests/fetch/history-questions-wrong-by-topic/{topic}', [UsersController::class, 'fetch_history_questions_wrong_by_topic_of_student'])->name('api.v1.users.student.tests.fetch-history-questions-wrong-by-topic-of-student');
Route::get('/users/student/tests/fetch/history-tests-completed', [UsersController::class, 'fetch_history_tests_completed_by_student'])->name('api.v1.users.student.tests.fetch-history-tests-completed-by-student');
Route::get('/users/student/tests/fetch/topics-available-in-tests', [UsersController::class, 'fetch_topics_available_in_tests'])->name('api.v1.users.student.tests.fetch-topics-available-in-tests');