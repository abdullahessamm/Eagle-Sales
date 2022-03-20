<?php

namespace App\Observers;

use App\Models\AppConfig;
use App\Models\DuesOfSeller;
use App\Models\SellerRegisterInvitation;

class SellerRegisterInvitationObserver
{

    private function getRewardAmount(): float
    {
        $invitationRewardAmount = AppConfig::where('key', 'sellers_invitation_reward_amount')->first();
        if (!$invitationRewardAmount)
            abort(500);

        return (float) $invitationRewardAmount->value;
    }

    /**
     * Handle the SellerRegisterInvitation "created" event.
     *
     * @param  \App\Models\SellerRegisterInvitation  $sellerRegisterInvitation
     * @return void
     */
    public function created(SellerRegisterInvitation $sellerRegisterInvitation)
    {
        $tax = AppConfig::where('key', 'sellers_invitation_reward_tax')->first();

        $duesOfSeller = new DuesOfSeller;
        $duesOfSeller->seller_id = $sellerRegisterInvitation->invitation_owner_id;
        $duesOfSeller->cash = $this->getRewardAmount();
        $duesOfSeller->is_reward = true;
        $duesOfSeller->register_invitation_id = $sellerRegisterInvitation->id;
        $duesOfSeller->reward_type = 'register invitation reward';
        $duesOfSeller->is_commission = false;
        $duesOfSeller->tax = $tax ? (float) $tax->value : 0;
        $duesOfSeller->save();
    }
}
