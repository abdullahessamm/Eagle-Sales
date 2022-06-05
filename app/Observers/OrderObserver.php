<?php

namespace App\Observers;

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
        $user = auth()->user()->userData;

        if ($user->isHierdSeller() || $user->isFreelancerSeller())
            $order->created_by = $user->userInfo->id;
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
