<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class KpiThresholdColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('kpi_threshold_colors')->delete();

        \DB::table('kpi_threshold_colors')->insert(array(
            0 => array(
                'id' => 1,
                'color' => 'Green',
                'color_code' => '#008000',
                'font_color' => '#fff',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'color' => 'Yellow',
                'color_code' => '#ffff00',
                'font_color' => '#000000',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'color' => 'Red',
                'color_code' => '#ff0000',
                'font_color' => '#fff',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            )
        ));
    }
}
