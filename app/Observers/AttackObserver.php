<?php

namespace App\Observers;

use App\Models\AttackAttempt;
use App\Models\BlockedIp;

class AttackObserver
{
    /**
     * Handle the AttackAttempt "created" event.
     *
     * @param  \App\Models\AttackAttempt  $attackAttempt
     * @return void
     */
    public function created(AttackAttempt $attackAttempt)
    {
        $ipBlocker = new BlockedIp();
        $ipBlocker->blockIfNotBlocked($attackAttempt->ip, $attackAttempt->attack_type . ' attacker');
    }
}
