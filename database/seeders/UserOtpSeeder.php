<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User_otp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserOtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ids=User::pluck('id');

        for ($i=0; $i < 3 ; $i++) {
                User_otp::create([
                'otp'=>rand(123456,999999),
                'user_id'=>$ids[rand(0,count($ids)-1)],
                'expired_at'=>now(),
               
            ]);
            
        }
    }
}
