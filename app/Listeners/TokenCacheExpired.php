<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TokenCacheExpired
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
        if ($event->type !== 'token')
            return;

        if (! $event->cacheData)
            return;
        
        if (! isset($event->cacheData['key']) || ! isset($event->cacheData['val']))
            return;

        cache()->put($event->cacheData['key'], $event->cacheData['val'], now()->addDays(2));
    }
}
