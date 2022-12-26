<?php

namespace App\Http\Resources\Api\Answer\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnswersForResolveTestByStudentCollection extends ResourceCollection
{
    public $collects = AnswersForResolveTestByStudentResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'utilities' => []
        ];
    }
}
