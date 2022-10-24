<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public $collects = ExampleResource::class;

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
