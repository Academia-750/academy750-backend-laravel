<?php

namespace App\Http\Resources\Api\Permission\v1;

use App\Http\Resources\Api\Role\v1\RoleCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => 'permissions',
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => [
                'permissionName' => $this->resource->name,
                'permissionAliasName' => $this->resource->alias_name ?: $this->resource->name,
                'guardNamePermission' => $this->resource->guard_name,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'roles' => RoleCollection::make($this->whenLoaded('roles'))
            ],
        ];
    }
}
