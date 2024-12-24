<?php

namespace Database\Seeders\User;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Constants\PaymentGatewayConst;
use App\Models\DeveloperApiCredential;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'firstname'         => "Test",
                'lastname'          => "User",
                'email'             => "user@appdevs.net",
                'username'          => "appdevs",
                'type'              => "personal",
                'mobile_code'       => "880",
                'mobile'            => "123456789",
                'full_mobile'       => "880123456789",
                'status'            => true,
                'password'          => Hash::make("appdevs"),
                'address'           => '{"country":"Bangladesh","city":"Dhaka","zip":"1230","state":"Bangladesh","address":"Dhaka,Bangladesh"}',
                'email_verified'    => true,
                'sms_verified'      => true,
                'kyc_verified'      => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'firstname'         => "Test",
                'lastname'          => "User2",
                'email'             => "user2@appdevs.net",
                'username'          => "testusr2",
                'type'              => "personal",
                'mobile_code'       => "880",
                'mobile'            => "123456781",
                'full_mobile'       => "880123456781",
                'status'            => true,
                'password'          => Hash::make("appdevs"),
                'address'           => '{"country":"Bangladesh","city":"Dhaka","zip":"1230","state":"Bangladesh","address":"Dhaka,Bangladesh"}',
                'email_verified'    => true,
                'sms_verified'      => true,
                'kyc_verified'      => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'firstname'         => "Test",
                'lastname'          => "Business",
                'email'             => "business@appdevs.net",
                'username'          => "business",
                'type'              => "business",
                'mobile_code'       => "880",
                'mobile'            => "12345678145645",
                'full_mobile'       => "88012345678145645",
                'status'            => true,
                'password'          => Hash::make("appdevs"),
                'address'           => '{"country":"Bangladesh","city":"Dhaka","zip":"1230","state":"Bangladesh","address":"Dhaka,Bangladesh"}',
                'email_verified'    => true,
                'sms_verified'      => true,
                'kyc_verified'      => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'firstname'         => "Test",
                'lastname'          => "Business2",
                'email'             => "business2@appdevs.net",
                'username'          => "business2",
                'type'              => "business",
                'mobile_code'       => "880",
                'mobile'            => "1234567545",
                'full_mobile'       => "88012344564",
                'status'            => true,
                'password'          => Hash::make("appdevs"),
                'address'           => '{"country":"Bangladesh","city":"Dhaka","zip":"1230","state":"Bangladesh","address":"Dhaka,Bangladesh"}',
                'email_verified'    => true,
                'sms_verified'      => true,
                'kyc_verified'      => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

        ];
        User::insert($data);

            // Merchant Developer API
            $data = [
                [
                    'user_id'       => 3,
                    'client_id'         => generate_unique_string("developer_api_credentials","client_id",100),
                    'client_secret'     => generate_unique_string("developer_api_credentials","client_secret",100),
                    'mode'              => PaymentGatewayConst::ENV_SANDBOX,
                    'status'            => true,
                    'created_at'        => now(),
                ],
                [
                    'user_id'       => 4,
                    'client_id'         => generate_unique_string("developer_api_credentials","client_id",100),
                    'client_secret'     => generate_unique_string("developer_api_credentials","client_secret",100),
                    'mode'              => PaymentGatewayConst::ENV_SANDBOX,
                    'status'            => true,
                    'created_at'        => now(),
                ],
            ];
    
            DeveloperApiCredential::insert($data);
    }
}
