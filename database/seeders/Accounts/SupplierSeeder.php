<?php

namespace Database\Seeders\Accounts;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Supplier::get()->first())
            return;

        $user_ids = [1, 6];

        for ($i=0; $i < 2; $i++)
            Supplier::create([
                'user_id'           => $user_ids[$i],
                'vat_no'            => 'sw1212' . $user_ids[$i],
                'shop_name'         => 'Shop ' . $i + 1,
                'whatsapp_no'       => '+9660115131618' . $i,
                'l1_address'        => $i . ' Test street Cairo Egypt',
                'l1_address_ar'     => $i . ' شارع تجربة القاهرة مصر',
            ]);
    }
}
