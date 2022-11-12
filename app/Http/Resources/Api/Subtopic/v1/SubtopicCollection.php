<?php

namespace App\Http\Resources\Api\Subtopic\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SubtopicCollection extends ResourceCollection
{
    //public $collects = ExampleResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'utilities' => []
        ];
    }
}
