<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;

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
    public function updateDataMyProfile($request)
    {
        return $this->DBQuery->updateDataMyProfile($request);
    }

    public function unsubscribeFromSystem()
    {
        return $this->DBQuery->unsubscribeFromSystem();
    }

    public function changePasswordAuth($request)
    {
        return $this->DBQuery->changePasswordAuth($request);
    }

    public function getNotificationsUser()
    {
        return $this->DBQuery->getNotificationsUser();
    }

    public function read_notification_user($notification_id)
    {
        return $this->DBQuery->read_notification_user($notification_id);
    }
}
