<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SetupSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetupSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'slug'          => "walletium",
            'image'         => 'seeder/white-fav.webp',
            'title'         => "Walletium - Your Ultimate Digital Mobile Wallet Solution.",
            'desc'          => "It is a cutting-edge digital mobile wallet solution designed to revolutionize the way you manage your finances. With a comprehensive suite of features, Walletium empowers users to seamlessly add, send, withdraw, and exchange money, all within a secure and intuitive platform.",
            'tags'          => ['Walletium',"digital wallet","ewallet","flutter app","gateway solutions","money transfer","payment gateway","developer api","gateway", "payment API","php wallet", "processor", "wallet app"],
            'last_edit_by'  => 1, 	 
        ];

        SetupSeo::firstOrCreate($data);
    }
}
