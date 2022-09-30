<?php

namespace App\Core\Resources\Profile;

use App\Core\Resources\Profile\Interfaces\ProfileInterface;
use App\Http\Resources\Api\Profile\ProfileResource;

class SchemaJson implements ProfileInterface
{

    protected CacheApp $cacheApp;

    public function __construct(CacheApp $cacheApp)
    {
        $this->cacheApp = $cacheApp;
    }

    public function getDataMyProfile()
    {
        return ProfileResource::make($this->cacheApp->getDataMyProfile());
    }
}
