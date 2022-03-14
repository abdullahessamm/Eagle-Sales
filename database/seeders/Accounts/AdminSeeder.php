<?php

namespace Database\Seeders\Accounts;

use App\Models\BackOfficeUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (BackOfficeUser::get()->first())
            return;

        $user_ids = [5, 10];

        for ($i=0; $i < 2; $i++)
            BackOfficeUser::create([
                'user_id'           => $user_ids[$i],
                'job_title'         => ['admin', 'moderator'][$i]
            ]);
    }
}
