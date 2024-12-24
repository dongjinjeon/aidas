<?php

namespace Database\Seeders\Admin;

use App\Models\Frontend\AnnouncementCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $announcement_categories = array(
            array('id' => '1','name' => '{"language":{"en":{"name":"Introduction"},"es":{"name":"Introducci\\u00f3n"},"ar":{"name":"\\u0645\\u0642\\u062f\\u0645\\u0629"},"fr":{"name":"Introduction"},"hi":{"name":"\\u092a\\u0930\\u093f\\u091a\\u092f"}}}','status' => '1','created_at' => '2023-12-25 09:44:06','updated_at' => '2024-10-28 08:36:53'),
            array('id' => '2','name' => '{"language":{"en":{"name":"Security Measures"},"es":{"name":"Medidas de seguridad"},"ar":{"name":"\\u062a\\u062f\\u0627\\u0628\\u064a\\u0631 \\u0623\\u0645\\u0646\\u064a\\u0629"},"fr":{"name":"Mesures de S\\u00e9curit\\u00e9"},"hi":{"name":null}}}','status' => '1','created_at' => '2023-12-25 09:44:29','updated_at' => '2024-10-28 08:38:17'),
            array('id' => '3','name' => '{"language":{"en":{"name":"FAQs About"},"es":{"name":"Preguntas frecuentes sobre"},"ar":{"name":"\\u0627\\u0644\\u0623\\u0633\\u0626\\u0644\\u0629 \\u0627\\u0644\\u0634\\u0627\\u0626\\u0639\\u0629 \\u062d\\u0648\\u0644"}}}','status' => '1','created_at' => '2023-12-25 09:44:56','updated_at' => '2024-04-04 09:22:58'),
            array('id' => '4','name' => '{"language":{"en":{"name":"Maximizing"},"es":{"name":"Maximizando"},"ar":{"name":"\\u062a\\u0639\\u0638\\u064a\\u0645"},"fr":{"name":"Maximiser"},"hi":{"name":"\\u0905\\u0927\\u093f\\u0915\\u0924\\u092e \\u0915\\u0930\\u0928\\u093e"}}}','status' => '1','created_at' => '2023-12-25 09:45:10','updated_at' => '2024-10-28 08:33:45')
          );
        AnnouncementCategory::insert($announcement_categories);
    }
}
