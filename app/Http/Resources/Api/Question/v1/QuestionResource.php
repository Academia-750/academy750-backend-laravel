<?php

namespace App\Http\Resources\Api\Question\v1;

use App\Http\Resources\Api\Answer\v1\AnswerCollection;
use App\Http\Resources\Api\Image\v1\ImageResource;
use App\Http\Resources\Api\TestModel\v1\TestResourceCollection as TestsCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'questions',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "question-text" => $this->resource->question,
                "reason-text" => $this->resource->reason,
                "is_visible" => $this->resource->is_visible === 'yes',
                'its_for_test' => $this->resource->its_for_test,
                'its_for_card_memory' => $this->resource->its_for_card_memory,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => [
                'answers' => $this->when(collect($this->resource)->has('answers'),
                    function () {
                        return AnswerCollection::make($this->resource->answers);
                    }),
                'tests' => $this->when(collect($this->resource)->has('tests'),
                    function () {
                        return TestsCollection::make($this->resource->tests);
                    }),
                'image' => $this->when(collect($this->resource)->has('image'),
                    function () {
                        return ImageResource::make($this->resource->image);
                    }),
                // 'questionable' => $this->when(collect($this->resource)->has('questionable'),
                //     function () {
                //         return $this->resource->questionable;
                //     })
            ]
        ];
    }
}
