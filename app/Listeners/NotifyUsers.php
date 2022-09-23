<?php

namespace App\Listeners;

use App\Events\Interfaces\ShouldNotifyListeners;

class NotifyUsers
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
    public function handle(ShouldNotifyListeners $event)
    {
        $listeners = $event->getListeners();
        $body      = $event->getNotificationBody();

        foreach ($listeners as $listener)
            $listener->notify(get_class($event), $body);

        return true;
    }
}
