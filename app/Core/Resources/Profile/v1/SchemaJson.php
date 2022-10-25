<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Http\Resources\Api\User\v1\UserResource;

class SchemaJson implements ProfileInterface
{

    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp)
    {
        $this->cacheApp = $cacheApp;
    }

    public function getDataMyProfile()
    {
        return UserResource::make($this->cacheApp->getDataMyProfile());
    }
}
