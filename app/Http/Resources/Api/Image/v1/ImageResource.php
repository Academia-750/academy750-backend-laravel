<?php

namespace App\Http\Resources\Api\Image\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
