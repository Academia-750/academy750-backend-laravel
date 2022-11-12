<?php

namespace App\Http\Resources\Api\TopicGroup\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TopicGroupCollection extends ResourceCollection
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
