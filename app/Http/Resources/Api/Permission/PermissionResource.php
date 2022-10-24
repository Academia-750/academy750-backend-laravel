<?php

namespace App\Http\Resources\Api\Permission;

use App\Http\Resources\Api\Role\RoleCollection;
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
                'createdAt' => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => [
                'roles' => RoleCollection::make($this->whenLoaded('roles'))
            ],
        ];
    }
}
