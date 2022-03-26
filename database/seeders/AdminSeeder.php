<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProfile;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data =[
            'email' => 'dreamaxtion@gmail.com',
            'username' => 'dreamaxtion',
            'password' => bcrypt('dreamguruAXTION!'),
            'referral_code' => Str::random(8),
        ];
        
        $user = new User($data);
        $user->role = 'admin';

        $user->save($data);

        $user_profile = new UserProfile([
            'name' => 'admin',
            'address' => NULL,
            'date_of_birth' => '2022-02-02',
            'gender' => 'male',
            'phone_number' => '08138259856',
            'avatar' => NULL,
        ]);
        
        $user->profile()->save($user_profile);
        
    }
}
