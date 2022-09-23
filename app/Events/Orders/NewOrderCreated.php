<?php

namespace App\Events\Orders;

use App\Events\Interfaces\ShouldNotifyListeners;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderCreated implements shouldBroadcast, ShouldNotifyListeners
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    protected $listeners = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order->load('billingAddress');
        $this->listeners[] = $this->order->supplier()->first()->getUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->listeners[0]->id);
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
