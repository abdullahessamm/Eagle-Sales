<?php

namespace App\Listeners\Accounts;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class ReactivateItemsForReactivatedSupplier
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = clone $event->user;
        if ($user->job !== User::SUPPLIER_JOB_NUMBER)
            return;
        
        $user->withFullInfo();
        $itemsOfSupplier = $user->userInfo->items;

        foreach ($itemsOfSupplier as $item)
            $item->activate(true);
    }
}
