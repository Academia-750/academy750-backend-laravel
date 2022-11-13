<?php

namespace App\Http\Resources\Api\Image\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ImageCollection extends ResourceCollection
{
    //public $collects = ExampleResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'utilities' => []
        ];
    }
}
