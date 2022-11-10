<?php

namespace App\Http\Resources\Api\Topic\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TopicCollection extends ResourceCollection
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
