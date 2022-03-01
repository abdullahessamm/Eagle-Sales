<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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
                'email'             => "user_$i@gmail.com",
                'username'          => "user_$i",
                'password'          => \Hash::make('password'),
                'is_active'         => true,
                'country'           => 'SA',
                'city'              => 'Gadda',
                'job'               => $i <= 3 ? $i : 0,
                'serial_code'       => $i . \Str::random(19)
            ]);
        }
    }
}
