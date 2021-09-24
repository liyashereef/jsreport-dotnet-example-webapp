<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class StcReportingTemplateRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('stc_reporting_template_rules')->delete();
        \DB::table('stc_reporting_template_rules')->insert(array(
            0 => array(
                'id' => 1,
                'color_id' => 1,
                'min_value' => 0,
                'max_value' => 35,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),

            ),
            1 => array(
                'id' => 2,
                'color_id' => 2,
                'min_value' => 35.00001,
                'max_value' => 70,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),

            ),
            2 => array(
                'id' => 3,
                'color_id' => 3,
                'min_value' => 70.00001,
                'max_value' => 100,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),

            ),
        ));
    }
}
