<?php

namespace App\Http\Resources\Api\User\v1;

use App\Http\Resources\Api\Image\v1\ImageResource;
use App\Http\Resources\Api\Role\v1\RoleCollection;
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
                "email_verified_at" => ($this->resource->email_verified_at !== null) ? $this->resource->email_verified_at->format('Y-m-d h:m:s') : null ,
                "last_session" => ($this->resource->last_session !== null) ? $this->resource->last_session->format('Y-m-d h:m:s') : null ,
                "created_at" => $this->resource->created_at->format('Y-m-d h:m:s')
            ],
            'relationships' => [
                //'roles' => \App\Http\Resources\Api\Role\RoleCollection::make($this->whenLoaded('roles'))
                'roles' => $this->when(collect($this->resource)->has('roles'),
                    function () {
                        return RoleCollection::make($this->resource->roles);
                    }),
                'image' => $this->when(collect($this->resource)->has('image'),
                    function () {
                        return ImageResource::make($this->resource->image);
                    })
            ],
            'meta' => [
                'unread-notifications' => $this->resource->unreadNotifications->count()
            ]
        ];
    }
}
