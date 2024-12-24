<?php

namespace Database\Seeders\Admin;

use App\Models\SendingPurpose;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SendingPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sending_purposes = array(
            array('added_by' => '1','name' => 'Home','slug' => 'home','status' => '1','created_at' => '2023-12-12 10:52:45','updated_at' => '2023-12-12 11:08:42'),
            array('added_by' => '1','name' => 'Salary','slug' => 'salary','status' => '1','created_at' => '2023-12-12 11:10:18','updated_at' => '2023-12-12 11:10:18'),
            array('added_by' => '1','name' => 'Medicine','slug' => 'medicine','status' => '1','created_at' => '2023-12-12 11:10:38','updated_at' => '2023-12-12 11:10:38')
          );

        SendingPurpose::insert($sending_purposes);
    }
}
