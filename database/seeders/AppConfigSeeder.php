<?php

namespace Database\Seeders;

use App\Models\AppConfig;
use Illuminate\Database\Seeder;

class AppConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (AppConfig::first())
            return;

        AppConfig::create([
            'key' => 'sellers_invitation_reward_enabled',
            'value' => '1'
        ]);

        AppConfig::create([
            'key' => 'sellers_invitation_reward_amount',
            'value' => '10'
        ]);

        AppConfig::create([
            'key' => 'sellers_invitation_reward_tax',
            'value' => '3'
        ]);

        // config for enable/disable auto-approve items
        AppConfig::create([
            'key' => 'auto_approve_items',
            'value' => '0'
        ]);

        // config for determine commission for sellers
        AppConfig::create([
            'key' => 'sellers_commission',
            'value' => '0'
        ]);

        // config for determine our commissions
        AppConfig::create([
            'key' => 'our_commission',
            'value' => '0'
        ]);

        // config for enable/disable auto-approve customers
        AppConfig::create([
            'key' => 'auto_approve_customers',
            'value' => '0'
        ]);

        // config for enable/disable auto-approve freelancer sellers
        AppConfig::create([
            'key' => 'auto_approve_freelancer_sellers',
            'value' => '0'
        ]);

        // send email to user after approval reply
        AppConfig::create([
            'key' => 'send_email_to_user_after_approval_reply',
            'value' => '0'
        ]);

        // send sms to user after approval reply
        AppConfig::create([
            'key' => 'send_sms_to_user_after_approval_reply',
            'value' => '0'
        ]);

        // config for enable/disable send email to supplier when item approval reply
        AppConfig::create([
            'key' => 'send_email_to_supplier_when_item_approval_reply',
            'value' => '0'
        ]);

        // send sms to supplier when item approval reply
        AppConfig::create([
            'key' => 'send_sms_to_supplier_when_item_approval_reply',
            'value' => '0'
        ]);

        // send email to users when order state changed
        AppConfig::create([
            'key' => 'send_email_to_users_when_order_state_changed',
            'value' => '1'
        ]);

        // send sms to users when order state changed
        AppConfig::create([
            'key' => 'send_sms_to_users_when_order_state_changed',
            'value' => '0'
        ]);
        
        // enable comments on items with default value true
        AppConfig::create([
            'key' => 'enable_comments_on_items',
            'value' => '1'
        ]);

        // enable ratings on items with default value true
        AppConfig::create([
            'key' => 'enable_ratings_on_items',
            'value' => '1'
        ]);

        // enable promotions on items with default value true
        AppConfig::create([
            'key' => 'enable_promotions_on_items',
            'value' => '1'
        ]);
        
        // order can be cancelled after approved with default value false
        AppConfig::create([
            'key' => 'order_can_be_cancelled_after_approved',
            'value' => '0'
        ]);
    }
}
