<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = array(
            array('admin_id' => '1','country' => 'United States','name' => 'United States dollar','code' => 'USD','symbol' => '$','type' => 'FIAT','flag' => 'seeder/841236c5-52b1-42ce-9b93-724fe78d3737.webp','rate' => '1.00000000','sender' => '1','receiver' => '1','default' => '1','status' => '1','created_at' => '2023-09-23 08:47:26','updated_at' => '2023-11-23 04:42:31'),
            array('admin_id' => '1','country' => 'United Kingdom','name' => 'British pound','code' => 'GBP','symbol' => '£','type' => 'FIAT','flag' => 'seeder/5ec588eb-b4f8-4add-b92d-3362a549005a.webp','rate' => '0.82000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-09-23 08:47:27','updated_at' => '2023-10-06 11:23:26'),
            array('admin_id' => '1','country' => 'Australia','name' => 'Australian dollar','code' => 'AUD','symbol' => '$','type' => 'FIAT','flag' => 'seeder/0e19a866-78a4-45ba-9541-baa7554967f5.webp','rate' => '1.55000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-09-23 08:47:27','updated_at' => '2023-10-05 06:19:59'),
            array('admin_id' => '1','country' => 'Canada','name' => 'Canadian dollar','code' => 'CAD','symbol' => '$','type' => 'FIAT','flag' => 'seeder/8aaf1aee-dcc9-45b5-9789-3cf204201e3c.webp','rate' => '1.37000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 06:12:35','updated_at' => '2023-10-05 06:19:10'),
            array('admin_id' => '1','country' => 'Germany','name' => 'Euro','code' => 'EUR','symbol' => '€','type' => 'FIAT','flag' => 'seeder/a986d49b-3b3f-42f2-9103-a3c75ce38a60.webp','rate' => '0.95000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 06:43:14','updated_at' => '2023-10-05 06:43:14'),
            array('admin_id' => '1','country' => 'Nigeria','name' => 'Nigerian naira','code' => 'NGN','symbol' => '₦','type' => 'FIAT','flag' => 'seeder/2096625b-d872-4585-a7b7-371b52726fbf.webp','rate' => '464.00000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 07:15:23','updated_at' => '2023-11-23 04:26:33'),
            array('admin_id' => '1','country' => 'Pakistan','name' => 'Pakistani rupee','code' => 'PKR','symbol' => 'Rs','type' => 'FIAT','flag' => 'seeder/03fd4e11-b129-4655-922b-8a7995615e1c.webp','rate' => '284.00000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 07:16:58','updated_at' => '2023-10-05 11:22:29'),
            array('admin_id' => '1','country' => 'India','name' => 'Indian rupee','code' => 'INR','symbol' => '₹','type' => 'FIAT','flag' => 'seeder/c5522c07-8249-48a3-8806-a9f84da176f6.webp','rate' => '83.24000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 07:25:19','updated_at' => '2023-10-05 07:25:20'),
            array('admin_id' => '1','country' => 'Bangladesh','name' => 'Bangladeshi taka','code' => 'BDT','symbol' => '৳','type' => 'FIAT','flag' => 'seeder/80e5d4d2-a018-4034-adca-809515cf3e82.webp','rate' => '110.00000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 07:27:08','updated_at' => '2023-10-05 07:27:08'),
            array('admin_id' => '1','country' => 'Kenya','name' => 'Kenyan shilling','code' => 'KES','symbol' => 'KSh','type' => 'FIAT','flag' => 'seeder/0027511b-115b-4255-bab6-a42416ee4689.webp','rate' => '148.60000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-05 07:28:41','updated_at' => '2023-10-05 07:28:41'),
            array('admin_id' => '1','country' => 'Cote D\'Ivoire (Ivory Coast)','name' => 'West African CFA franc','code' => 'XOF','symbol' => 'CFA','type' => 'FIAT','flag' => 'seeder/0d280d76-395c-4497-95d6-e915c740753c.webp','rate' => '622.24000000','sender' => '1','receiver' => '1','default' => '0','status' => '1','created_at' => '2023-10-06 05:35:00','updated_at' => '2023-10-06 05:35:01')
          );

        Currency::insert($currencies);
    }
}
