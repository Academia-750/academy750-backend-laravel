<?php

namespace App\Http\Resources\Api\TopicGroup\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicGroupResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'resources',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [

            ],
            'relationships' => [

            ]
        ];
    }
}
