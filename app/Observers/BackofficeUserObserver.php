<?php

namespace App\Observers;

use App\Models\BackOfficeUser;

class BackofficeUserObserver
{

    /**
     * Handle the BackOfficeUser "updated" event.
     *
     * @param  \App\Models\BackOfficeUser  $backOfficeUser
     * @return void
     */
    public function updated(BackOfficeUser $backOfficeUser)
    {
        $user = $backOfficeUser->getUser();
        $user->updated_at = now();
        if (auth()->user())
            $user->updated_by = auth()->user()->userData->id;
        $user->save();
    }
}
