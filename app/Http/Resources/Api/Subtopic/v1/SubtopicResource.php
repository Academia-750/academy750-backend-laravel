<?php

namespace App\Http\Resources\Api\Subtopic\v1;

use App\Http\Resources\Api\Opposition\v1\OppositionCollection;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Topic\v1\TopicCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class SubtopicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'subtopics',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "name" => $this->resource->name,
                "is_available" => $this->resource->is_available,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'meta' => [
                'total_questions_subtopic' => $this->resource->questions->count()
            ],
            'relationships' => [
                'topics' => $this->when(collect($this->resource)->has('topics'),
                    function () {
                        return TopicCollection::make($this->resource->topics);
                    }),
                'oppositions' => $this->when(collect($this->resource)->has('oppositions'),
                    function () {
                        return OppositionCollection::make($this->resource->oppositions);
                    }),
                'questions' => $this->when(collect($this->resource)->has('questions'),
                    function () {
                        return QuestionCollection::make($this->resource->questions);
                    })
            ]
        ];
    }
}
