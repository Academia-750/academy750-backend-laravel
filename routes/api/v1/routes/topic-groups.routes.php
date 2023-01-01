<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TopicGroupsController;

// Rutas del recurso TopicGroups

Route::get('topic-groups', [TopicGroupsController::class, 'index'])->name('api.v1.topic-groups.index');
Route::get('topic-groups/{topic_group}', [TopicGroupsController::class, 'read'])->name('api.v1.topic-groups.read');
