<?php

namespace Database\Seeders\User;

use App\Models\Admin\Currency;
use App\Models\Merchant\SandboxWallet;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SandboxWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies_ids = Currency::roleHasOne()->active()->get()->pluck("id")->toArray();

        $user_ids = [3,4];

        foreach($user_ids as $user_id) {
            foreach($currencies_ids as $currency_id) {
                $data[] = [
                    'user_id'   => $user_id,
                    'currency_id'   => $currency_id,
                    'balance'       => 0,
                    'status'        => true,
                ];
            }
        }

        SandboxWallet::upsert($data,['user_id','currency_id'],['balance']);
    }
}
