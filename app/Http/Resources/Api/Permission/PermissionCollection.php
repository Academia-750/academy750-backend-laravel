<?php

namespace App\Http\Resources\Api\Permission;

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
