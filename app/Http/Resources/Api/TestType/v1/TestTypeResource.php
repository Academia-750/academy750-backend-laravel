<?php

namespace App\Http\Resources\Api\TestType\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TestTypeResource extends JsonResource
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
