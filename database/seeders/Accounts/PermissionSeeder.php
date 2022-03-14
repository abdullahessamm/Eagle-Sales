<?php

namespace Database\Seeders\Accounts;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Permission::get()->first())
            return;
        
        for ($i = 0; $i < 2; $i++)
            Permission::create([
                'backoffice_user_id' => $i + 1,
                'suppliers_access_level' => $i === 0 ? '1111' : '0110',
                'customers_access_level' => $i === 0 ? '1111' : '0110',
                'sellers_access_level' => $i === 0 ? '1111' : '0110',
                'categorys_access_level' => $i === 0 ? '1111' : '0110',
                'items_access_level' => $i === 0 ? '1111' : '0110',
                'backoffice_emps_access_level' => $i === 0 ? '1111' : '0110',
                'orders_access_level' => $i === 0 ? '1111' : '0110',
                'commissions_access_level' => $i === 0 ? '1111' : '0110',
                'journey_plan_access_level' => $i === 0 ? '1111' : '0110',
                'pricelists_access_level' => $i === 0 ? '1111' : '0110',
                'app_config_access' => $i === 0 ? true : false,
            ]);
    }
}
