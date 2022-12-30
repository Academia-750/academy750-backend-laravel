<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\TopicsController;

// Rutas del recurso Topics

Route::get('topics', [TopicsController::class, 'index'])->name('api.v1.topics.index');
Route::get('topics/available/by-generate-test', [TopicsController::class, 'get_topics_available_for_create_test'])->name('api.v1.topics.topics-available-por-create-test');
Route::get('topics/{topic}', [TopicsController::class, 'read'])->name('api.v1.topics.read');
Route::post('topics/create', [TopicsController::class, 'create'])->name('api.v1.topics.create');
Route::patch('topics/update/{topic}', [TopicsController::class, 'update'])->name('api.v1.topics.update');
Route::delete('topics/delete/{topic}', [TopicsController::class, 'delete'])->name('api.v1.topics.soft-delete');
Route::post('topics/actions-on-multiple-records', [TopicsController::class, 'action_for_multiple_records'])->name('api.v1.topics.actions-on-multiple-records');
Route::post('topics/import', [TopicsController::class, 'import_records'])->name('api.v1.topics.import');

Route::get("topics/{topic}/oppositions-available", [TopicsController::class, 'get_oppositions_available_of_topic'])->name('api.v1.topics.oppositions-available');

Route::get("topics/{topic}/relationship/subtopics", [TopicsController::class, 'get_relationship_subtopics'])->name('api.v1.topics.relationships.subtopics');
Route::get("topics/{topic}/relationship/subtopics/{subtopic}", [TopicsController::class, 'get_relationship_a_subtopic'])->name('api.v1.topics.relationships.subtopics.record');
Route::post("topics/{topic}/relationship/subtopics/create", [TopicsController::class, 'create_relationship_subtopic'])->name('api.v1.topics.create.relationships.subtopic');
Route::patch("topics/{topic}/relationship/subtopics/{subtopic}/update", [TopicsController::class, 'update_relationship_subtopic'])->name('api.v1.topics.update.relationships.subtopic');
Route::delete("topics/{topic}/relationship/subtopics/{subtopic}/delete", [TopicsController::class, 'delete_relationship_subtopic'])->name('api.v1.topics.delete.relationships.subtopic');

Route::get("topics/{topic}/relationship/subtopics/{subtopic}/relationship/questions", [TopicsController::class, 'subtopics_get_relationship_questions'])->name('api.v1.topics.relationships.subtopics.relationships.questions');
Route::get("topics/{topic}/relationship/subtopics/{subtopic}/relationship/questions/{question}", [TopicsController::class, 'subtopics_get_relationship_a_question'])->name('api.v1.topics.relationships.subtopics.relationships.questions.record');

Route::get("topics/{topic}/relationship/oppositions", [TopicsController::class, 'get_relationship_oppositions'])->name('api.v1.topics.relationships.oppositions');
Route::get("topics/{topic}/relationship/oppositions/{opposition}/subtopics", [TopicsController::class, 'get_relationship_a_opposition'])->name('api.v1.topics.relationships.oppositions.record.subtopics');
Route::post("topics/{topic}/relationship/oppositions/assign", [TopicsController::class, 'assign_opposition_with_subtopics_to_topic'])->name('api.v1.topics.relationships.oppositions.assign');
Route::patch("topics/{topic}/relationship/oppositions/{opposition}/update/subtopics", [TopicsController::class, 'update_subtopics_opposition_by_topic'])->name('api.v1.topics.relationships.oppositions.update.subtopics');
Route::delete("topics/{topic}/relationship/oppositions/{opposition}/delete", [TopicsController::class, 'delete_opposition_by_topic'])->name('api.v1.topics.relationships.oppositions.delete');

Route::get('topics/relationship/questions', [TopicsController::class, 'topic_relationship_questions'])->name('api.v1.topics.relationship.questions');

Route::get('topics/import/template', [TopicsController::class, 'download_template_import_records'])->name('api.v1.topics.import.template');
/*
Route::post('topics/export', [TopicsController::class, 'export_records'])->name('api.v1.topics.export');
Route::post('topics/import', [TopicsController::class, 'import_records'])->name('api.v1.topics.import');
Route::get('topics/import/template', [TopicsController::class, 'download_template_import_records'])->name('api.v1.topics.import.template');
*/
