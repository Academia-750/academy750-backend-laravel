<?php

namespace App\Listeners\Api;

use App\Events\Api\ActionUserLogoutEvent;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserLogoutListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event): void
    {
        ActionUserLogoutEvent::dispatch([
            "user" => $event->user
        ]);
    }
}
