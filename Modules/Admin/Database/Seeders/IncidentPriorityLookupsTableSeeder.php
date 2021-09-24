<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class IncidentPriorityLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('incident_priority_lookups')->delete();

        \DB::table('incident_priority_lookups')->insert(array(
            1 => array(
                'id' => 1,
                'value' => 'Low',
                'created_at' => '2019-07-15 18:30:00',
                'updated_at' => '2019-07-15 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 2,
                'value' => 'Medium',
                'created_at' => '2019-07-15 18:30:00',
                'updated_at' => '2019-07-15 18:30:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 3,
                'value' => 'High',
                'created_at' => '2019-07-15 18:30:00',
                'updated_at' => '2019-07-15 18:30:00',
                'deleted_at' => null,
            )
        ));

    }
}
