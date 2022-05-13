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

        $namesAr = [
            'أحمد',
            'على',
            'عبدالله',
            'صلاح',
            'سامح',
            'محمد',
            'عبدالرحمن',
            'محمود',
            'تامر',
            'إيهاب'
        ];

        $reversedNames = array_reverse($names);
        $reversedArNames = array_reverse($namesAr);

        for($i=0; $i<10; $i++) {
            $userLetter = '';

            $job = $i;

            if ($i > 4)
                $job = $i - 5;
            

            switch ($job) {
                case 0:
                    $userLetter = 'S';
                    break;
                case 1:
                    $userLetter = 'HS';
                    break;
                case 2:
                    $userLetter = 'FS';
                    break;
                case 3:
                    $userLetter = 'C';
                    break;
                case 4:
                    $userLetter = 'A';
                    break;
            }
            User::create([
                'f_name'            => $names[$i],
                'f_name_ar'         => $namesAr[$i],
                'l_name'            => $reversedNames[$i],
                'l_name_ar'         => $reversedArNames[$i],
                'email'             => "user" . $i + 1 . "@gmail.com",
                'username'          => "user" . $i + 1,
                'password'          => \Hash::make('password'),
                'is_active'         => true,
                'country'           => 'SA',
                'city'              => 'Gadda',
                'job'               => $job,
                'serial_code'       => $userLetter . $i + 1 . '_' . \Str::random(25 - strlen($userLetter . $i + 1 . '_')),
                'is_approved'       => true,
                'gender'            => 'male'
            ]);
        }
    }
}
