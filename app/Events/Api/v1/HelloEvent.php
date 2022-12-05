<?php

namespace App\Events\Api\v1;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HelloEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('notify-channel-import-process')
        ];
    }

    public function broadcastAs(): string
    {
        return 'name.custom.event';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->data
        ];
    }

    public function broadcastWhen(): bool
    {
        return $this->data !== null; // Si da true el evento se transmite, de lo contrario no se transmitirá a nadie.
    }
}
