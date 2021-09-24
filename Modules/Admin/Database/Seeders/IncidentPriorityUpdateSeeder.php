<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class IncidentPriorityUpdateSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('incident_priority_lookups')->where('value','Low')->update(['priority_order' => 3]);
        \DB::table('incident_priority_lookups')->where('value','Medium')->update(['priority_order' => 2]);
        \DB::table('incident_priority_lookups')->where('value','High')->update(['priority_order' => 1]);

    }
}
