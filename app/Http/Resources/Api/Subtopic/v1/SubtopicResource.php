<?php

namespace App\Http\Resources\Api\Subtopic\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class SubtopicResource extends JsonResource
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
