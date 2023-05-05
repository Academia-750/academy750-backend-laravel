<?php

use App\Http\Controllers\Api\v1\QuestionsController;
use App\Http\Controllers\Api\v1\TopicsController;

Route::get('topics/{topic}/relationship/questions', [QuestionsController::class, 'topics_relationship_get_questions'])->name('api.v1.topics.relationship.questions.index');
Route::get('topics/{topic}/relationship/questions/{question}', [QuestionsController::class, 'topic_relationship_questions_read'])->name('api.v1.topics.relationship.questions.read');
Route::post('topics/{topic}/relationship/questions/create', [QuestionsController::class, 'topic_relationship_questions_create'])->name('api.v1.topics.relationship.questions.create');
Route::post('topics/{topic}/relationship/questions/update/{question}', [QuestionsController::class, 'topic_relationship_questions_update'])->name('api.v1.topics.relationship.questions.update');
Route::delete('topics/{topic}/relationship/questions/delete/{question}', [QuestionsController::class, 'topic_relationship_questions_delete'])->name('api.v1.topics.relationship.questions.delete');

Route::get('topics/relationship/questions', [TopicsController::class, 'topic_relationship_questions'])->name('api.v1.topics.relationship.questions');
