<?php

namespace App\Http\Resources\Api\TestType\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TestTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'tests-type',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
                'alias_name' => $this->resource->alias_name
            ],
            'relationships' => [

            ]
        ];
    }
}
