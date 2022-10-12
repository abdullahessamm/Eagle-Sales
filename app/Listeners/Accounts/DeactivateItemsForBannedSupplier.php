<?php

namespace App\Listeners\Accounts;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeactivateItemsForBannedSupplier
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
            $item->activate(false);
    }
}
