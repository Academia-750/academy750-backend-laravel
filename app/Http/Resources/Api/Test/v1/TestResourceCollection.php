<?php

namespace App\Http\Resources\Api\Test\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TestResourceCollection extends ResourceCollection
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
