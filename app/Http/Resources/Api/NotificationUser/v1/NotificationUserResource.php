<?php

namespace App\Http\Resources\Api\NotificationUser\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationUserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'notifications',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "type" => $this->resource->type,
                "body" => $this->resource->data,
                'read-at' => $this->resource->read_at ? $this->resource->read_at->format('Y-m-d h:m:s'): null,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s'),
                "created-at-diff" => $this->resource->created_at->diffForHumans()
            ],
            'relationships' => []
        ];
    }
}
