<?php

namespace App\Http\Resources\Api\User\v1;

use App\Http\Resources\Api\Group\v1\GroupResource;
use App\Http\Resources\Api\Image\v1\ImageResource;
use App\Http\Resources\Api\Role\v1\RoleCollection;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class UserResource extends JsonResource
{
    #[ArrayShape(['type' => "string", 'id' => "string", 'attributes' => "array", 'relationships' => "array", 'meta' => "array"])] public function toArray($request): array
    {
        return [
            'type' => 'users',
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => [
                'dni' => $this->resource->dni,
                'first_name' => $this->resource->first_name,
                'last_name' => $this->resource->last_name,
                'phone' => $this->resource->phone,
                'state_account' => $this->resource->state,
                'email' => $this->resource->email,
                "email_verified_at" => ($this->resource->email_verified_at !== null) ? date('Y-m-d H:i:s', strtotime($this->resource->email_verified_at)) : null,
                "last_session" => ($this->resource->last_session !== null) ? date('Y-m-d H:i:s', strtotime($this->resource->last_session)) : null,
                "created_at" => date('Y-m-d H:i:s', strtotime($this->resource->created_at))
            ],
            'relationships' => [
                'roles' => RoleCollection::make($this->resource->roles),
                'image' => ImageResource::make($this->resource->image),
                'groups' => $this->resource->groups()->whereNull('discharged_at')->join('groups', 'groups.id', 'group_users.group_id')->select('groups.id', 'groups.name')->get(),

            ],
            'meta' => [
                'unread-notifications' => $this->resource->unreadNotifications->count()
            ]
        ];
    }
}