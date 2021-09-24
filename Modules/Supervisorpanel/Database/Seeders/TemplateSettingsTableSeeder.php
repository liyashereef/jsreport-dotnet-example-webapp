<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;

class TemplateSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('template_settings')->delete();
        \DB::table('template_settings')->insert([
            'id' => 1,
            'min_value' => 0,
            'max_value' => 10,
            'last_update_limit' => 2,
            'color_id' => 4,
            'created_at' => NULL,
            'updated_at' => NULL,
        ]);       
    }
}
