<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\QuestionsController;

// Rutas del recurso Questions

Route::get('subtopics/{subtopic}/relationship/questions', [QuestionsController::class, 'subtopics_relationship_get_questions'])->name('api.v1.subtopics.relationship.questions.index');
Route::get('subtopics/{subtopic}/relationship/questions/{question}', [QuestionsController::class, 'subtopic_relationship_questions_read'])->name('api.v1.subtopics.relationship.questions.read');
Route::post('subtopics/{subtopic}/relationship/questions/create', [QuestionsController::class, 'subtopic_relationship_questions_create'])->name('api.v1.subtopics.relationship.questions.create');
Route::patch('subtopics/{subtopic}/relationship/questions/update/{question}', [QuestionsController::class, 'subtopic_relationship_questions_update'])->name('api.v1.subtopics.relationship.questions.update');
Route::delete('subtopics/{subtopic}/relationship/questions/delete/{question}', [QuestionsController::class, 'subtopic_relationship_questions_delete'])->name('api.v1.subtopics.relationship.questions.delete');

Route::get('topics/{topic}/relationship/questions', [QuestionsController::class, 'topics_relationship_get_questions'])->name('api.v1.topics.relationship.questions.index');
Route::get('topics/{topic}/relationship/questions/{question}', [QuestionsController::class, 'topic_relationship_questions_read'])->name('api.v1.topics.relationship.questions.read');
Route::post('topics/{topic}/relationship/questions/create', [QuestionsController::class, 'topic_relationship_questions_create'])->name('api.v1.topics.relationship.questions.create');
Route::post('topics/{topic}/relationship/questions/update/{question}', [QuestionsController::class, 'topic_relationship_questions_update'])->name('api.v1.topics.relationship.questions.update');
Route::delete('topics/{topic}/relationship/questions/delete/{question}', [QuestionsController::class, 'topic_relationship_questions_delete'])->name('api.v1.topics.relationship.questions.delete');

//Route::post('subtopics/relationship/questions/actions-on-multiple-records', [QuestionsController::class, 'subtopic_relationship_questions_action_for_multiple_records'])->name('api.v1.subtopic.relationship.questions.actions-on-multiple-records');
//Route::post('subtopics/relationship/questions/import', [QuestionsController::class, 'subtopic_relationship_questions_import_records'])->name('api.v1.subtopic.relationship.questions.import');
/*
Route::post('questions/export', [QuestionsController::class, 'export_records'])->name('api.v1.questions.export');
Route::post('questions/import', [QuestionsController::class, 'import_records'])->name('api.v1.questions.import');
Route::get('questions/import/template', [QuestionsController::class, 'download_template_import_records'])->name('api.v1.questions.import.template');
*/
