<?php

namespace App\Observers;

use App\Http\Controllers\PhoneController;
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
        PhoneController::sendCode($phone);
    }
}
