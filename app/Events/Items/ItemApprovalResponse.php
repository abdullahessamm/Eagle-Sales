<?php

namespace App\Events\Items;

use App\Events\Interfaces\ShouldNotifyListeners;
use App\Models\Item;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemApprovalResponse implements ShouldBroadcast, ShouldNotifyListeners
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Item $item;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $userId = $this->item->supplier->user_id;
        return new PrivateChannel('user.' . $userId);
    }

    public function getListeners(): array
    {
        return [$this->item->supplier->getUser()];
    }

    public function getNotificationBody(): string
    {
        return json_encode($this->item);
    }
}
