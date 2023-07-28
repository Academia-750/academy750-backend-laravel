<?php

namespace App\Http\Resources\Api\Question\v1;

use App\Models\Question;
use App\Http\Resources\Api\Answer\v1\AnswersForResolveTestByStudentCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionByTestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'questions-test',
            'id' => $this->resource->uuid,
            'attributes' => [
                "question-text" => $this->resource->question,
                "reason-text" => $this->resource->reason
            ],
            'relationships' => [
                'answers-test' => AnswersForResolveTestByStudentCollection::make($this->resource->answers)
            ]
        ];
    }
}
