<?php

namespace App\Observers;

use App\Events\Orders\NewOrderCreated;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function creating(Order $order)
    {
        $user = auth()->user();
        $user = $user->userData;
        $order->created_by = $user->id;

        // hanlde currency
        $order->currency = $order->supplier()->first()->currency;
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        event(new NewOrderCreated($order));
    }

    /**
     * Handle the Order "updating" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updating(Order $order)
    {
        $user = auth()->user()->userData;
        $order->updated_by = $user->id;
    }
}
