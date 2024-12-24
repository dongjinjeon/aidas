<?php

namespace App\Traits\User;

use Exception;
use App\Models\UserWallet;
use App\Models\Admin\Currency;
use App\Constants\PaymentGatewayConst;
use App\Models\Merchant\SandboxWallet;
use App\Models\Merchant\DeveloperApiCredential;

trait RegisteredUsers {
    protected function createUserWallets($user) {
        $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
        $wallets = [];
        foreach($currencies as $currency_id) {
            $wallets[] = [
                'user_id'       => $user->id,
                'currency_id'   => $currency_id,
                'balance'       => 0,
                'status'        => true,
                'created_at'    => now(),
            ];
        }

        try{
            UserWallet::insert($wallets);
        }catch(Exception $e) {
            // handle error
            throw new Exception("Failed to create wallet! Please try again");
        }
    }
    protected function createDeveloperApi($user) {
        try{
            DeveloperApiCredential::create([
                'user_id'       => $user->id,
                'client_id'         => generate_unique_string("developer_api_credentials","client_id",100),
                'client_secret'     => generate_unique_string("developer_api_credentials","client_secret",100),
                'mode'              => PaymentGatewayConst::ENV_SANDBOX,
                'status'            => true,
                'created_at'        => now(),
            ]);

            // create developer sandbox wallets
            $this->createSandboxWallets($user);
        }catch(Exception $e) { 
            throw new Exception("Failed to create developer API. Something went wrong!");
        }
    }

    protected function createSandboxWallets($user) {
        if(!$user->developerApi) return false;

        $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
        $wallets = [];
        foreach($currencies as $currency_id) {
            $wallets[] = [
                'user_id'   => $user->id,
                'currency_id'   => $currency_id,
                'balance'       => 0,
                'status'        => true,
                'created_at'    => now(),
            ];
        }

        try{
            SandboxWallet::insert($wallets);
        }catch(Exception $e) {
            // handle error
        }
    }
}