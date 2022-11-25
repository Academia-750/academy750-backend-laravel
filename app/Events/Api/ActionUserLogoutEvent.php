<?php

namespace App\Events\Api;

use App\Http\Resources\Api\User\v1\UserResource;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\ArrayShape;

class ActionUserLogoutEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function broadcastOn(): array
    {
        \Log::debug("Ejecutar ActionUserLogoutEvent");
        \Log::debug($this->user->getRouteKey());


        return [
            //new Channel('public-channel'),
            //new PrivateChannel("App.Models.User.{$this->user->getRouteKey()}"),
            new Channel('notifications-authentication')
            //new PresenceChannel('presence-channel')
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.logout.academia750';
    }

    #[ArrayShape(['data' => "mixed"])] public function broadcastWith(): array
    {
        return [
            'data' => UserResource::make(
                $this->user
            )
        ];
    }
}
