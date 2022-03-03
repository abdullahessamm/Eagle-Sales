<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CacheExpired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $type;
    public string $action;
    public array|null $cacheData;

    /**
     * 
     *
     * @param string $type (Available 'token')
     * @param string $action (Available 'regenerate')
     * @param array $cacheData
     */
    public function __construct(string $type, string $action, array $cacheData = [])
    {
        $this->type = $type;
        $this->action = $action;
        $this->cacheData = count($cacheData) > 0 ? $cacheData : null;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
