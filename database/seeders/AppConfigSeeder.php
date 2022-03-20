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
    }
}
