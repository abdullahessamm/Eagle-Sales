<?php

namespace App\Events\Items;

use App\Events\Interfaces\ShouldNotifyListeners;
use App\Models\Item;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemRated implements ShouldBroadcast, ShouldNotifyListeners
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Rate $rate;
    protected array $listeners = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Rate $rate, Item $item, User $user)
    {
        $this->rate = $rate;
        $this->rate->item = $item->only(['id', 'name', 'ar_name']);
        $this->rate->user = $user->only(['id', 'f_name', 'l_name', 'avatar_uri', 'avatar_pos']);
        $this->listeners[] = $item->supplier->getUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $listenerId = $this->listeners[0]->id;
        return new PrivateChannel("user.$listenerId");
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function getNotificationBody(): string
    {
        return json_encode($this->rate);
    }
}
