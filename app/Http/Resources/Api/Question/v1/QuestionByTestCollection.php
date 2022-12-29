<?php

namespace App\Http\Resources\Api\Question\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionByTestCollection extends ResourceCollection
{
    public $collects = QuestionByTestResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'utilities' => []
        ];
    }
}
