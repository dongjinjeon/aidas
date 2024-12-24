<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SetupPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SetupPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setup_pages = array(
            array('id' => '1','type' => 'setup-page','slug' => 'home','title' => 'Home','url' => '/','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL),
            array('id' => '2','type' => 'setup-page','slug' => 'developer','title' => 'Developer','url' => '/developer','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL),
            array('id' => '3','type' => 'setup-page','slug' => 'about-us','title' => 'About Us','url' => '/about-us','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL),
            array('id' => '4','type' => 'setup-page','slug' => 'services','title' => 'Services','url' => '/services','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL),
            array('id' => '5','type' => 'setup-page','slug' => 'web-journal','title' => 'Web Journal','url' => '/web-journal','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL),
            array('id' => '6','type' => 'setup-page','slug' => 'contact-us','title' => 'Contact','url' => '/contact-us','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL), 
            array('id' => '9','type' => 'useful-links','slug' => 'faq','title' => 'FAQ','url' => '/faq','details' => NULL,'last_edit_by' => '1','status' => '1','created_at' => '2023-12-24 04:09:28','updated_at' => NULL), 
          );

        SetupPage::insert($setup_pages);
    }
}
