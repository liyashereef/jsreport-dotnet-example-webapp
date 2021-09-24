<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;

class IncidentStatusListTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('incident_status_lists')->delete();

        \DB::table('incident_status_lists')->insert(array(
            0 => array(
                'id' => 1,
                'status' => 'Open',
                'created_at' => null,
                'updated_at' => null,
            ),
            1 => array(
                'id' => 2,
                'role' => 'In Progress',
                'created_at' => null,
                'updated_at' => null,
            ),
            2 => array(
                'id' => 3,
                'role' => 'Closed',
                'created_at' => null,
                'updated_at' => null,
            ),            
        ));
    }
}
