<?php

namespace App\Http\Resources\Api\Questionnaire\v1;

use App\Http\Resources\Api\Opposition\v1\OppositionResource;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Subtopic\v1\SubtopicCollection;
use App\Http\Resources\Api\TestType\v1\TestTypeResource;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use App\Http\Resources\Api\User\v1\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'tests',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                'number_of_questions_requested' => $this->resource->number_of_questions_requested,
                'number_of_questions_generated' => $this->resource->number_of_questions_generated,
                'test_result' => $this->resource->test_result,
                'total_questions_correct' => $this->resource->total_questions_correct,
                'total_questions_wrong' => $this->resource->total_questions_wrong,
                'total_questions_unanswered' => $this->resource->total_questions_unanswered,
                'is_solved_test' => $this->resource->is_solved_test,
                'test_type' => $this->resource->test_type,
                "finished_at" => $this->resource->finished_at ? date('Y-m-d H:i:s', strtotime($this->resource->finished_at)) : null,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'opposition' => $this->when(collect($this->resource)->has('opposition'),
                    function () {
                        return OppositionResource::make($this->resource->opposition);
                    }),
                'user' => $this->when(collect($this->resource)->has('user'),
                    function () {
                        return UserResource::make($this->resource->user);
                    }),
                'questions' => $this->when(collect($this->resource)->has('questions'),
                    function () {
                        return QuestionCollection::make($this->resource->questions);
                    }),
                'topics' => $this->when(collect($this->resource)->has('topics'),
                    function () {
                        return TopicCollection::make($this->resource->topics);
                    }),
                'subtopics' => $this->when(collect($this->resource)->has('subtopics'),
                    function () {
                        return SubtopicCollection::make($this->resource->subtopics);
                    }),
            ]
        ];
    }
}
