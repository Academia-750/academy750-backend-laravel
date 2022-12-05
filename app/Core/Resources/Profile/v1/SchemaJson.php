<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Http\Resources\Api\User\v1\UserResource;
use App\Http\Resources\Api\v1\NotificationUser\NotificationUserCollection;
use Illuminate\Support\Facades\Auth;

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

    public function updateDataMyProfile($request)
    {
        return UserResource::make($this->cacheApp->updateDataMyProfile($request));
    }

    public function unsubscribeFromSystem()
    {
        return response()->json([
            'message' => $this->cacheApp->unsubscribeFromSystem()
        ], 200);
    }

    public function changePasswordAuth($request)
    {
        return response()->json([
            'message' => $this->cacheApp->changePasswordAuth($request)
        ], 200);
    }

    public function getNotificationsUser()
    {
        return NotificationUserCollection::make(
            $this->cacheApp->getNotificationsUser()
        )->additional([
            'meta' => [
                'unread_notifications_count' => Auth::user()?->unreadNotifications->count()
            ]
        ]);
    }

    public function read_notification_user($notification_id)
    {
        return NotificationUserCollection::make(
            $this->cacheApp->read_notification_user($notification_id)
        )->additional([
            'meta' => [
                'unread_notifications_count' => Auth::user()?->unreadNotifications->count()
            ]
        ]);
    }
}
