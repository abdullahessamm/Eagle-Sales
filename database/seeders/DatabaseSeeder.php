<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Database\Seeders\Accounts\UserSeeder as AccountsUserSeeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Accounts\UserSeeder::class,
            Accounts\SupplierSeeder::class,
            Accounts\AdminSeeder::class,
            Accounts\PermissionSeeder::class,
            AppConfigSeeder::class,
        ]);
    }
}
