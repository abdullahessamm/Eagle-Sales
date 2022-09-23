<?php

namespace App\Events\Orders;

use App\Events\Interfaces\ShouldNotifyListeners;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStateChanged implements ShouldBroadcast, ShouldNotifyListeners
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    protected array $listeners = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->handleListeners();
    }

    protected function handleListeners()
    {
        $updater = $this->order->updater()->first();
        if (! $updater->isSupplier())
            $this->listeners[] = $this->order->supplier()->first()->getUser();

        if (! $updater->isCustomer() && ! $updater->isOnlineClient())
            $this->listeners[] = $this->order->buyer()->first();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = collect($this->listeners)
            ->map(fn ($listener) => new PrivateChannel("user.$listener->id"))
            ->toArray();
        
        return $channels;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function getNotificationBody(): string
    {
        return json_encode($this->order);
    }
}
