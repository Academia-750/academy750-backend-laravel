<?php

namespace App\Http\Resources\Api\TestModel\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TestModelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'resources',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => [

            ]
        ];
    }
}
