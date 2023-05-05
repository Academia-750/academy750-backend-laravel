<?php

use App\Http\Controllers\Api\v1\TopicsController;

Route::get("topics/{topic}/oppositions-available", [TopicsController::class, 'get_oppositions_available_of_topic'])->name('api.v1.topics.oppositions-available');

Route::get("topics/{topic}/relationship/oppositions", [TopicsController::class, 'get_relationship_oppositions'])->name('api.v1.topics.relationships.oppositions');
Route::get("topics/{topic}/relationship/oppositions/{opposition}/subtopics", [TopicsController::class, 'get_relationship_subtopics_by_opposition'])->name('api.v1.topics.relationships.oppositions.record.subtopics');
Route::post("topics/{topic}/relationship/oppositions/assign", [TopicsController::class, 'assign_opposition_with_subtopics_to_topic'])->name('api.v1.topics.relationships.oppositions.assign');
Route::patch("topics/{topic}/relationship/oppositions/{opposition}/update/subtopics", [TopicsController::class, 'update_subtopics_opposition_by_topic'])->name('api.v1.topics.relationships.oppositions.update.subtopics');
Route::delete("topics/{topic}/relationship/oppositions/{opposition}/delete", [TopicsController::class, 'delete_opposition_by_topic'])->name('api.v1.topics.relationships.oppositions.delete');
