<?php

namespace App\Observers;

use App\Models\BlockedIp;

class BlockedIpObserver
{

    /**
     * Handle the BlockedIp "deleted" event.
     *
     * @param  \App\Models\BlockedIp  $blockedIp
     * @return void
     */
    public function deleted(BlockedIp $blockedIp)
    {
        if (cache()->has('blocked_ips')) {
            $blockedIps = cache()->get('blocked_ips');
            $indexOfIP = array_search($blockedIp->ip, $blockedIps);

            if (! is_integer($indexOfIP))
                return;
            
            unset($blockedIps[$indexOfIP]);
            cache()->put('blocked_ips', $blockedIps);
        }
    }

    
    public function retrieved(BlockedIp $blockedIp)
    {
        $blockedIps = [];

        if (cache()->has('blocked_ips'))
            $blockedIps = cache()->get('blocked_ips');
        
        if (in_array($blockedIp->ip, $blockedIps))
            return;
        
        $blockedIps[] = $blockedIp->ip;
        cache()->put('blocked_ips', $blockedIps, now()->addYears(50));
    }
}
