<?php

namespace App\Observers;

use App\Models\AppConfig;
use App\Models\Item;

class ItemObserver
{
    /**
     * Handle the Item "creating" event.
     *
     * @param  \App\Models\Item  $item
     * @return void
     */
    public function creating(Item $item)
    {
        $this->handleCurrency($item);
    }

    private function handleCurrency(Item $item)
    {
        $supplier = $item->supplier();
        if ($supplier) {
            $item->currency = $supplier->currency;
        }
    }

    /**
     * Handle the Item "created" event.
     *
     * @param  \App\Models\Item  $item
     * @return void
     */
    public function created(Item $item)
    {
        if ($autoApprove = AppConfig::where('key', 'auto_approve_items')->first()) {
            $autoApproveEnabled = (bool) $autoApprove->value;
            if ($autoApproveEnabled) return;
        }

        event(new \App\Events\Items\NewItemCreated($item));
    }
}
