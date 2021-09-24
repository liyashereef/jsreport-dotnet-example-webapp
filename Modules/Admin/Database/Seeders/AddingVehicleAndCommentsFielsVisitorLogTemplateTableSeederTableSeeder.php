<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AddingVehicleAndCommentsFielsVisitorLogTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('visitor_log_template_fields')->insert(array(
            0 => array(
                'template_id' => 0,
                'fieldname' => 'vehicle_reference',
                'field_displayname' => 'Vehicle Reference',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                'template_id' => 0,
                'fieldname' => 'work_location',
                'field_displayname' => 'Work Location',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            2 => array(
                'template_id' => 0,
                'fieldname' => 'additional_comments',
                'field_displayname' => 'Additional Comments',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            )
        ));
    }
}
