<?php

namespace App\Http\Resources\Api\Student\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'users',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [

            ],
            'relationships' => [

            ],
            'links' => [
                'self' => route('api.v1.example.read', $this->resource->getRouteKey()),
                'related' => [
                    'example' => route('api.v1.example.read', $this->resource->getRouteKey()) . '?include=example-relation',
                ]
            ]
        ];
    }
}
