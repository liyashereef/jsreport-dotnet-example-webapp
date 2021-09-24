<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class VisitorLogTemplateFieldsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('visitor_log_template_fields')->delete();

        \DB::table('visitor_log_template_fields')->insert(array(
            0 => array(
                'id' => 1,
                'template_id' => 0,
                'fieldname' => 'first_name',
                'field_displayname' => 'Full Name',
                'field_type' => 1,
                'is_required' => 1,
                'is_visible' => 1,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'template_id' => 0,
                'fieldname' => 'visitor_type_id',
                'field_displayname' => 'Visitor Type',
                'field_type' => 2,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
           2 => array(
                'id' => 3,
                'template_id' => 0,
                'fieldname' => 'phone',
                'field_displayname' => 'Phone',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
           3 => array(
                'id' => 4,
                'template_id' => 0,
                'fieldname' => 'email',
                'field_displayname' => 'Email',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
           4 => array(
                'id' => 5,
                'template_id' => 0,
                'fieldname' => 'checkin',
                'field_displayname' => 'Check In',
                'field_type' => 5,
                'is_required' => 1,
                'is_visible' => 1,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),

           5 => array(
                'id' => 6,
                'template_id' => 0,
                'fieldname' => 'name_of_company',
                'field_displayname' => 'Name of the Company',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
           6 => array(
                'id' => 7,
                'template_id' => 0,
                'fieldname' => 'whom_to_visit',
                'field_displayname' => 'Person you are visiting',
                'field_type' => 1,
                'is_required' => 0,
                'is_visible' => 0,
                'is_custom' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
           7 => array(
                'id' => 8,
                'template_id' => 0,
                'fieldname' => 'license_number',
                'field_displayname' => 'Vehicle License Plate Number',
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
