<?php

namespace App\Listeners\Items;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class SendMailToSupplier
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
        $item = $event->item;
        if (! (bool) $item->is_approved)
            return;

        $itemApprovalMailIsEnabled = AppConfig::where('key', 'send_email_to_supplier_when_item_approval_reply')->first();
        if (! $itemApprovalMailIsEnabled)
            return;

        if ( (bool) $itemApprovalMailIsEnabled->value ) {
            $supplier = $item->supplier()->getUser();
            $supplier->sendMail(new \App\Mail\Items\SendApprovalMailToSupplier($item, $supplier));
        }
    }
}
