<?php

namespace App\Http\Resources\Api\Opposition\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class OppositionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'oppositions',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
                'period' => $this->resource->period,
                'is_visible' => $this->resource->is_visible,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => []
        ];
    }
}
