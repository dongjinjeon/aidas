<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AppOnboardScreens;
use Illuminate\Database\Seeder;

class OnboardScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app_onboard_screens = array(
            array('id' => '1','title' => 'Easy, Quick & Secure System','sub_title' => 'Walletium has the most secure system which is very useful for money transactions.','image' => 'seeder/onboard.png','status' => '1','last_edit_by' => '1','created_at' => now(),'updated_at' => now()),
            array('id' => '2','title' => 'Send Money','sub_title' => 'Easy Way to Send Money Anytime.','image' => 'seeder/onboard2.png','status' => '1','last_edit_by' => '1','created_at' => now(),'updated_at' => now()),
            array('id' => '3','title' => 'Deposit & Withdraw Money','sub_title' => 'Easy Way to Deposit & Withdraw Money.','image' => 'seeder/onboard3.png','status' => '1','last_edit_by' => '1','created_at' => now(),'updated_at' => now()),
          
          );
        AppOnboardScreens::insert($app_onboard_screens);
    }
}
