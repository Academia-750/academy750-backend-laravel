<?php

namespace App\Core\Resources\Profile;

use App\Core\Resources\Profile\Interfaces\ProfileInterface;

class CacheApp implements ProfileInterface
{
    protected DBQuery $DBQuery;

    public function __construct(DBQuery $DBQuery)
    {
        $this->DBQuery = $DBQuery;
    }

    public function getDataMyProfile()
    {
        return $this->DBQuery->getDataMyProfile();
    }
}
