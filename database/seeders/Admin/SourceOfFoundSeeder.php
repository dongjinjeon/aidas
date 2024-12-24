<?php

namespace Database\Seeders\Admin;

use App\Models\SourceOfFound;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SourceOfFoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $source_of_founds = array(
            array('id' => '3','added_by' => '1','name' => 'Company','slug' => 'company','status' => '1','created_at' => '2023-12-12 12:03:52','updated_at' => '2023-12-12 12:07:41'),
            array('id' => '4','added_by' => '1','name' => 'Personal','slug' => 'personal','status' => '1','created_at' => '2023-12-12 12:08:03','updated_at' => '2023-12-12 12:08:03'),
            array('id' => '5','added_by' => '1','name' => 'Family','slug' => 'family','status' => '1','created_at' => '2023-12-12 12:08:20','updated_at' => '2023-12-12 12:08:20')
          );

          SourceOfFound::insert($source_of_founds);
    }
}
