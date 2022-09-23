<?php

namespace App\Events\Items;

use App\Events\Interfaces\ShouldNotifyListeners;
use App\Models\Item;
use App\Models\Permission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewItemCreated implements ShouldBroadcast, ShouldNotifyListeners
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Item $item;
    private array $listeners;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
        $this->listeners = Permission::getUsersCan('update', $item);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return collect($this->listeners)
            ->map(fn ($user) => new PrivateChannel("user.$user->id"))
            ->toArray();
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function getNotificationBody(): string
    {
        return json_encode($this->item);
    }
}
