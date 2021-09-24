<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class VisitorLogTypeLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        
        \DB::table('visitor_log_type_lookups')->delete();

        \DB::table('visitor_log_type_lookups')->insert(array(
            1 => array(
                'id' => 1,
                'type' => 'Visitor',
                'created_at' => '2019-01-03 18:30:00',
                'updated_at' => '2019-01-03 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 2,
                'type' => 'Employee',
                'created_at' => '2019-01-03 18:30:00',
                'updated_at' => '2019-01-03 18:30:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 3,
                'type' => 'Contractor',
                'created_at' => '2019-01-03 18:30:00',
                'updated_at' => '2019-01-03 18:30:00',
                'deleted_at' => null,
            ),
        ));

    }
}
