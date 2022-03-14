<?php

namespace Database\Seeders\Accounts;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;

class UserSeeder extends Seeder
{

    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::get()->first())
            return;

        $names = [
            'Ahmed',
            'Ali',
            'Abdullah',
            'Salah',
            'Sameh',
            'Mohamed',
            'Abdulrahman',
            'Mahmoud',
            'Tamer',
            'Ehab'
        ];

        $reversedNames = array_reverse($names);

        for($i=0; $i<10; $i++) {
            User::create([
                'f_name'            => $names[$i],
                'l_name'            => $reversedNames[$i],
                'email'             => "user_" . $i + 1 . "@gmail.com",
                'username'          => "user_" . $i + 1,
                'password'          => \Hash::make('password'),
                'is_active'         => true,
                'country'           => 'SA',
                'city'              => 'Gadda',
                'job'               => $i <= 4 ? $i : $i-5,
                'serial_code'       => $i . \Str::random(19),
                'is_approved'       => true,
            ]);
        }
    }
}
