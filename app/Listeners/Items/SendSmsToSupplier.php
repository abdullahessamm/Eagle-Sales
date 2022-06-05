<?php

namespace App\Listeners\Items;

use App\Models\AppConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSmsToSupplier
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

        $itemApprovalSmsIsEnabled = AppConfig::where('key', 'send_sms_to_supplier_when_item_approval_reply')->first();
        if (! $itemApprovalSmsIsEnabled)
            return;

        if ( (bool) $itemApprovalSmsIsEnabled->value ) {
            $supplier = $item->supplier()->getUser();
            $supplier->sendSms("Your item \"$item->name\" has been approved,\nYou can now start selling it on our platform.");
        }
    }
}
