<?php

namespace App\Http\Resources\Api\Answer\v1;

use App\Http\Resources\Api\Question\v1\QuestionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'answers',
            // UUID
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                // Needed from some relations in the frontend (Is the INT ID not the UUID)
                "id" => $this->resource->id,
                "answer_text" => $this->resource->answer,
                "is_grouper_answer" => $this->resource->is_grouper_answer,
                "is_correct_answer" => $this->resource->is_correct_answer,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'question' => $this->when(
                    collect($this->resource)->has('question'),
                    function () {
                        return QuestionResource::make($this->resource->question);
                    }
                )
            ]
        ];
    }
}