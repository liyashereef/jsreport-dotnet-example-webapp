<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;

class TemplateSettingRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('template_setting_rules')->insert([
            'id' => 1,
            'template_setting_id' => 1,
            'color_id' => 1,
            'min_value' => 0,
            'max_value' => 10,
            'created_at' => NULL,
            'updated_at' => NULL,
        ]);
    }
}
