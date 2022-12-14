<?php

namespace App\Http\Resources\Api\Image\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'images',
            'id' => $this->resource->getRouteKey(),
            'attributes' => [
                "path" => $this->resource->path,
                "type_path" => $this->resource->type_path,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => []
        ];
    }
}
