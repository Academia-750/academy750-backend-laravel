<?php

use App\Http\Controllers\Api\v1\QuestionsController;
use App\Http\Controllers\Api\v1\TopicsController;

Route::get("topics/{topic}/relationship/subtopics/{subtopic}/relationship/questions", [TopicsController::class, 'subtopics_get_relationship_questions'])->name('api.v1.topics.relationships.subtopics.relationships.questions');
Route::get("topics/{topic}/relationship/subtopics/{subtopic}/relationship/questions/{question}", [TopicsController::class, 'subtopics_get_relationship_a_question'])->name('api.v1.topics.relationships.subtopics.relationships.questions.record');

Route::get('subtopics/{subtopic}/relationship/questions', [QuestionsController::class, 'subtopics_relationship_get_questions'])->name('api.v1.subtopics.relationship.questions.index');
Route::get('subtopics/{subtopic}/relationship/questions/{question}', [QuestionsController::class, 'subtopic_relationship_questions_read'])->name('api.v1.subtopics.relationship.questions.read');
Route::post('subtopics/{subtopic}/relationship/questions/create', [QuestionsController::class, 'subtopic_relationship_questions_create'])->name('api.v1.subtopics.relationship.questions.create');
Route::post('subtopics/{subtopic}/relationship/questions/update/{question}', [QuestionsController::class, 'subtopic_relationship_questions_update'])->name('api.v1.subtopics.relationship.questions.update');
Route::delete('subtopics/{subtopic}/relationship/questions/{question}/delete', [QuestionsController::class, 'subtopic_relationship_questions_delete'])->name('api.v1.subtopics.relationship.questions.delete');
