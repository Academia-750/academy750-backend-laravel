<?php


use App\Http\Controllers\Api\v1\TopicsController;

Route::get("topics/{topic}/relationship/subtopics", [TopicsController::class, 'get_relationship_subtopics'])->name('api.v1.topics.relationships.subtopics');
Route::get("topics/{topic}/relationship/subtopics/{subtopic}", [TopicsController::class, 'get_relationship_a_subtopic'])->name('api.v1.topics.relationships.subtopics.record');
Route::post("topics/{topic}/relationship/subtopics/create", [TopicsController::class, 'create_relationship_subtopic'])->name('api.v1.topics.create.relationships.subtopic');
Route::patch("topics/{topic}/relationship/subtopics/{subtopic}/update", [TopicsController::class, 'update_relationship_subtopic'])->name('api.v1.topics.update.relationships.subtopic');
Route::delete("topics/{topic}/relationship/subtopics/{subtopic}/delete", [TopicsController::class, 'delete_relationship_subtopic'])->name('api.v1.topics.delete.relationships.subtopic');

Route::get('subtopics/import/template', [TopicsController::class, 'download_template_import_subtopics_records'])->name('api.v1.topics.import.subtopics.template');
Route::post('topics/subtopics/import', [TopicsController::class, 'import_subtopics_by_topics'])->name('api.v1.topics.import.subtopics');
