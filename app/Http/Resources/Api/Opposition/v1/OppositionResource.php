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
                'is_available' => $this->resource->is_available,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => []
        ];
    }
}
