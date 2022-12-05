<?php

namespace App\Core\Resources\Profile\v1\Interfaces;

interface ProfileInterface
{
    public function getDataMyProfile ();
    public function updateDataMyProfile ($request);
    public function unsubscribeFromSystem ();
    public function changePasswordAuth ($request);
    public function getNotificationsUser ();
    public function read_notification_user($notification_id);
}
