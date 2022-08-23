<?php

namespace App\Observers;

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
}
