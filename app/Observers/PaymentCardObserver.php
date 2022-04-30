<?php

namespace App\Observers;

use App\Models\PaymentCard;

class PaymentCardObserver
{
    /**
     * Handle the PaymentCard "created" event.
     *
     * @param  \App\Models\PaymentCard  $paymentCard
     * @return void
     */
    public function created(PaymentCard $paymentCard)
    {
        //TODO check if card is valid with bank api
    }

    /**
     * Handle the PaymentCard "restored" event.
     *
     * @param  \App\Models\PaymentCard  $paymentCard
     * @return void
     */
    public function restored(PaymentCard $paymentCard)
    {
        // hide card number and assign it to object
        $paymentCard->card_no = $paymentCard->getCardNumberAsHidden();
    }
}
