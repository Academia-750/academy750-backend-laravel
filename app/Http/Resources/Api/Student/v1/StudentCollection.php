<?php

namespace App\Http\Resources\Api\Student\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StudentCollection extends ResourceCollection
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
