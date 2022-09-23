<?php

namespace App\Listeners\Orders;

use App\Models\AppConfig;
use App\Models\DuesOfSeller;
use App\Models\OurCommission;

class CalculateCommissions
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
        $order = $event->order;
        
        if (! $order->isDelivered())
            return;

        $this->saveCompanyCommission(clone $order);
        $this->saveSellerCommission(clone $order);
        if ($order->requireShipping()) {
            $this->saveSupplierDues(clone $order);
            $this->calculateShippingDues(clone $order);
        }
    }

    protected function saveCompanyCommission($order)
    {
        OurCommission::create([
            'order_id' => $order->id,
            'supplier_id' => $order->supplier_id,
            'amount' => $order->calculateCommission(AppConfig::ourCommission())
        ]);
    }

    protected function saveSellerCommission($order)
    {
        $buyer = $order->buyer;
        if (! $buyer->linked_seller || $buyer->sellerReachedCommissionLimit())
            return;

        if ($buyer->linkedSeller()->first()->isHierdSeller())
            return;

        $sellerId = $buyer->linkedSeller->withFullInfo()->userInfo->id;
        $commission = $order->calculateCommission(AppConfig::sellersCommission());

        DuesOfSeller::createCommission($sellerId, $commission, $order->id, 0);
    }

    #TODO save supplier dues and calculate shipping dues after activate shipping
    protected function saveSupplierDues($order)
    {
        // code...
    }

    protected function calculateShippingDues($order)
    {
        // code...
    }
}
