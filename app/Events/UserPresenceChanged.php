<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class UserPresenceChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'presence.changed';
    }
}
