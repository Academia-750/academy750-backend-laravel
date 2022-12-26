<?php

namespace App\Http\Resources\Api\Answer\v1;

use App\Http\Resources\Api\Question\v1\QuestionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswersForResolveTestByStudentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'answers-test',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "answer_text" => $this->resource->answer
            ]
        ];
    }
}
