<?php

namespace App\Listeners\Accounts;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommitNewUserArrivedNotification
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
        $listeners = $event->listeners;
        foreach ($listeners as $listener) {
            $listener->notify(get_class($event), json_encode($event->user));
        }
    }
}
