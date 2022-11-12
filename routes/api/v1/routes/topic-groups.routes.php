<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TopicGroupsController;

// Rutas del recurso TopicGroups

Route::get('topic-groups', [TopicGroupsController::class, 'index'])->name('api.v1.topic-groups.index');
Route::get('topic-groups/{topic_group}', [TopicGroupsController::class, 'read'])->name('api.v1.topic-groups.read');
Route::post('topic-groups/create', [TopicGroupsController::class, 'create'])->name('api.v1.topic-groups.create');
Route::patch('topic-groups/update/{topic_group}', [TopicGroupsController::class, 'update'])->name('api.v1.topic-groups.update');
Route::delete('topic-groups/delete/{topic_group}', [TopicGroupsController::class, 'delete'])->name('api.v1.topic-groups.soft-delete');
Route::post('topic-groups/actions-on-multiple-records', [TopicGroupsController::class, 'action_for_multiple_records'])->name('api.v1.topic-groups.actions-on-multiple-records');
/*
Route::post('topic-groups/export', [TopicGroupsController::class, 'export_records'])->name('api.v1.topic-groups.export');
Route::post('topic-groups/import', [TopicGroupsController::class, 'import_records'])->name('api.v1.topic-groups.import');
Route::get('topic-groups/import/template', [TopicGroupsController::class, 'download_template_import_records'])->name('api.v1.topic-groups.import.template');
*/
