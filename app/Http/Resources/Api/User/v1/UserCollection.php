<?php

namespace App\Http\Resources\Api\User\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public $collects = UserResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
