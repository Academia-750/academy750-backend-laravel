<?php

namespace App\Http\Resources\Api\Permission\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCollection extends ResourceCollection
{
    public $collects = PermissionResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
