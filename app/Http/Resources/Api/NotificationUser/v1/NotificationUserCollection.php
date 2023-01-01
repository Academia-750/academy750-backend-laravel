<?php

namespace App\Http\Resources\Api\NotificationUser\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationUserCollection extends ResourceCollection
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
