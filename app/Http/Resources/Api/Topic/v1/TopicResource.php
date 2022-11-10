<?php

namespace App\Http\Resources\Api\Topic\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
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
