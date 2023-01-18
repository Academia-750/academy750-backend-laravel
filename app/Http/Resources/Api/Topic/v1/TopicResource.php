<?php

namespace App\Http\Resources\Api\Topic\v1;

use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Subtopic\v1\SubtopicCollection;
use App\Http\Resources\Api\TestModel\v1\QuestionnaireCollection;
use App\Http\Resources\Api\TopicGroup\v1\TopicGroupResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'topics',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
                'is_available' => $this->resource->is_available,
                'is_available_bool' => $this->resource->is_available === 'yes',
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'topic_group' => $this->when(collect($this->resource)->has('topic_group'),
                    function () {
                        return TopicGroupResource::make($this->resource->topic_group);
                    }),
                'subtopics' => $this->when(collect($this->resource)->has('subtopics'),
                    function () {
                        return SubtopicCollection::make($this->resource->subtopics);
                    }),
                'questions' => $this->when(collect($this->resource)->has('questions'),
                    function () {
                        return QuestionCollection::make($this->resource->questions);
                    }),
                'tests' => $this->when(collect($this->resource)->has('tests'),
                    function () {
                        return QuestionnaireCollection::make($this->resource->tests);
                    }),
            ],
            'meta' => [
                'has_subtopics' => $this->resource->subtopics->count() > 0
            ]
        ];
    }
}
