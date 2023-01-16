<?php

namespace App\Core\Resources\Questions\v1\Services;

use App\Core\Services\ManageImagesStorage;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SaveQuestionsService
{
    public static function saveQuestion ($request, $instanceModel) {

        return $instanceModel->questions()->create([
            'question' => $request->get('question-text'),
            'reason' => $request->get('reason-question'),
            'is_question_binary_alternatives' => $request->get('is-question-binary-alternatives'),
            'is_visible' => (bool) $request->get('is-visible') ? 'yes' : 'no',
            "its_for_test" => (bool) $request->get('is-test') ? 'yes' : 'no',
            "its_for_card_memory" => (bool) $request->get('is-card-memory') ? 'yes' : 'no',
        ]);
    }

    public static function validateImageWithFails ($file): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make([
            'image' => $file
        ], [
            'image' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,gif', 'max:10000'] // 10 mb
        ]);
    }

    public static function saveImageFileQuestion ($request, $questionInstance, $relativePath) {

        $validator = self::validateImageWithFails($request->file('file-reason'));

        if ($validator->fails()) {
            return null;
        }

        // Guarda solo la ruta relativa para acceder al Storage Publico {urlApi}/storage/...
        $path = Storage::url($request->file('file-reason')->store($relativePath));

        return $questionInstance->image()->create([
            'path' => $path,
            'type_path' => 'local'
        ]);
    }

    public static function updateImageQuestionInStorage ($request, $questionInstance, $relativePath) {
        $validator = self::validateImageWithFails($request->file('file-reason'));

        if (!$validator->fails()) {

            if ($questionInstance->image && $questionInstance->image->type_path === 'local') {

                //$path = "/storage/questions/images/topics/zHobBUFgM32kgVL0vuF268oJYLyKtjyQWvdb6sp9.jpg";
                $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel($questionInstance, "/");

                ManageImagesStorage::deleteImageStorage($nameFileStorage);

                $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel($questionInstance, "\\");

                ManageImagesStorage::deleteImageStorage($nameFileStorage);

            }

            $questionInstance->image()?->delete();
        }

        return self::saveImageFileQuestion($request, $questionInstance, $relativePath);
    }

    public static function updateQuestion ($request, Question $question): Question
    {
        $question->question = $request->get('question-text');
        $question->reason = $request->get('reason-question') ?? $question->reason;
        $question->is_visible = (bool) $request->get('is-visible') ? 'yes' : 'no';
        $question->is_question_binary_alternatives = (bool) !$request->get('is-test') ? 'not_defined' : $request->get('is-question-binary-alternatives');
        $question->its_for_test = (bool) $request->get('is-test') ? 'yes' : 'no';
        $question->its_for_card_memory = (bool) $request->get('is-card-memory') ? 'yes' : 'no';
        $question->save();

        return $question;
    }

    public static function getAnswersByQuestion ($request, $question): array
    {

        if (!$request->get('is-test')) {
            $answers = [
                [
                    'id' => $request->get('answer-correct-id'),
                    'answer' => $request->get('answer-correct'),
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'yes',
                    'question_id' => $question->getRouteKey(),
                ]
            ];

            return $answers;
        }


        if ($request->get('is-question-binary-alternatives') === 'yes' && $request->get('is-test')) {
            $answers = [
                [
                    'id' => $request->get('answer-correct-id'),
                    'answer' => $request->get('answer-correct'),
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'yes',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'id' => $request->get('another-answer-binary-alternative-id'),
                    'answer' => $request->get('another-answer-binary-alternative'),
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ]
            ];

            shuffle($answers);

            return $answers;
        }

        $answers = [
            [
                'id' => $request->get('answer-correct-id'),
                'answer' => $request->get('answer-correct'),
                'is_grouper_answer' => $request->get('is-grouper-answer-correct') ? 'yes' : 'no',
                'is_correct_answer' => 'yes',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-one-id'),
                'answer' => $request->get('answer-one'),
                'is_grouper_answer' => $request->get('is-grouper-answer-one') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-two-id'),
                'answer' => $request->get('answer-two'),
                'is_grouper_answer' => $request->get('is-grouper-answer-two') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
            [
                'id' => $request->get('answer-three-id'),
                'answer' => $request->get('answer-three'),
                'is_grouper_answer' => $request->get('is-grouper-answer-three') ? 'yes' : 'no',
                'is_correct_answer' => 'no',
                'question_id' => $question->getRouteKey(),
            ],
        ];

        shuffle($answers);

        return $answers;
    }
}
