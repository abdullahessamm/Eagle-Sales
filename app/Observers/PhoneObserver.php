<?php

namespace App\Observers;

use App\Models\Phone;

class PhoneObserver
{

    /**
     * Handle the Phone "created" event.
     *
     * @param  \App\Models\Phone  $phone
     * @return void
     */
    public function created(Phone $phone)
    {
        $phone->sendVerifyCode();
    }
}
