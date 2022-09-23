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

        AppConfig::updateOrCreate([
            'key' => 'sellers_invitation_reward_enabled',
            'value' => '1'
        ]);

        AppConfig::updateOrCreate([
            'key' => 'sellers_invitation_reward_amount',
            'value' => '10'
        ]);

        AppConfig::updateOrCreate([
            'key' => 'sellers_invitation_reward_tax',
            'value' => '3'
        ]);

        // config for enable/disable auto-approve items
        AppConfig::updateOrCreate([
            'key' => 'auto_approve_items',
            'value' => '0'
        ]);

        // config for determine commission for sellers
        AppConfig::updateOrCreate([
            'key' => 'sellers_commission'
        ], [
            'value' => '0'
        ]);

        // config for determine our commissions
        AppConfig::updateOrCreate([
            'key' => 'our_commission'
        ], [
            'value' => '0'
        ]);

        // config for enable/disable auto-approve customers
        AppConfig::updateOrCreate([
            'key' => 'auto_approve_customers',
            'value' => '0'
        ]);

        // config for enable/disable auto-approve freelancer sellers
        AppConfig::updateOrCreate([
            'key' => 'auto_approve_freelancer_sellers',
            'value' => '0'
        ]);

        // config for enable/disable auto-approve online clients
        AppConfig::updateOrCreate([
            'key' => 'auto_approve_online_clients',
            'value' => '1'
        ]);

        // send email to user after approval reply
        AppConfig::updateOrCreate([
            'key' => 'send_email_to_user_after_approval_reply',
            'value' => '0'
        ]);

        // send sms to user after approval reply
        AppConfig::updateOrCreate([
            'key' => 'send_sms_to_user_after_approval_reply',
            'value' => '0'
        ]);

        // config for enable/disable send email to supplier when item approval reply
        AppConfig::updateOrCreate([
            'key' => 'send_email_to_supplier_when_item_approval_reply',
            'value' => '0'
        ]);

        // send sms to supplier when item approval reply
        AppConfig::updateOrCreate([
            'key' => 'send_sms_to_supplier_when_item_approval_reply',
            'value' => '0'
        ]);

        // send email to users when order state changed
        AppConfig::updateOrCreate([
            'key' => 'send_email_to_users_when_order_state_changed',
            'value' => '1'
        ]);

        // send sms to users when order state changed
        AppConfig::updateOrCreate([
            'key' => 'send_sms_to_users_when_order_state_changed',
            'value' => '0'
        ]);
        
        // enable comments on items with default value true
        AppConfig::updateOrCreate([
            'key' => 'enable_comments_on_items',
            'value' => '1'
        ]);

        // enable ratings on items with default value true
        AppConfig::updateOrCreate([
            'key' => 'enable_ratings_on_items',
            'value' => '1'
        ]);

        // enable promotions on items with default value true
        AppConfig::updateOrCreate([
            'key' => 'enable_promotions_on_items',
            'value' => '1'
        ]);
        
        // order can be cancelled after approved with default value false
        AppConfig::updateOrCreate([
            'key' => 'order_can_be_cancelled_after_approved',
            'value' => '0'
        ]);

        AppConfig::updateOrCreate([
            'key' => 'max_commissions_num_for_sellers',
            'value' => '5'
        ]);

        // journey plans
        AppConfig::updateOrCreate([
            'key' => 'journy_plan_distance_to_mark_visit',
            'value' => '5'
        ]);
    }
}
