<?php

namespace App\Http\Resources\Api\Opposition\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OppositionCollection extends ResourceCollection
{
    //public $collects = ExampleResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => route('api.v1.example.index'),
            ],
            'utilities' => [

            ]
        ];
    }
}
