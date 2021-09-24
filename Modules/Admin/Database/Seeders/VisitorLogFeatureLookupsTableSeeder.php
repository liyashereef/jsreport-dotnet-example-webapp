<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class VisitorLogFeatureLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('visitor_log_template_features')->delete();

        \DB::table('visitor_log_template_features')->insert(array(
            0 => array(
                'id' => 1,
                'template_id' => 0,
                'feature_name' => 'picture',
                'feature_displayname' => 'Visitor Image',
                'is_required' => 0,
                'is_visible' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'template_id' => 0,
                'feature_name' => 'signature',
                'feature_displayname' => 'Visitor Signature',
                'is_required' => 0,
                'is_visible' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ),
        ));

    }
}
    