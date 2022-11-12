<?php

namespace App\Http\Resources\Api\TestType\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TestTypeCollection extends ResourceCollection
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
