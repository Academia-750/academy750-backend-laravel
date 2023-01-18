<?php

namespace App\Http\Resources\Api\Role\v1;

use App\Http\Resources\Api\Permission\v1\PermissionCollection;
use App\Http\Resources\Api\User\v1\UserCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "type" => "roles",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "roleName" => $this->resource->name,
                "roleAliasName" => $this->resource->alias_name ?: $this->resource->name,
                "createdAt" => date('Y-m-d H:i:s', strtotime($this->resource->created_at)),
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'permissions' => $this->when(collect($this->resource)->has('permissions'),
                    function () {
                        return PermissionCollection::make($this->resource->permissions);
                    }),
                'users' => $this->when(collect($this->resource)->has('users'),
                    function () {
                        return UserCollection::make($this->resource->users);
                    }),
            ],
        ];
    }
}
