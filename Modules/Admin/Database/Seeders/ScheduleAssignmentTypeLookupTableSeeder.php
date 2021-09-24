<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class ScheduleAssignmentTypeLookupTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('schedule_assignment_type_lookups')->delete();

        \DB::table('schedule_assignment_type_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'type' => 'Scheduled Backfill',
                'is_deletable' => 1,
                'created_at' => '2017-12-27 06:03:59',
                'updated_at' => '2017-12-27 06:03:59',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'type' => 'Unscheduled Backfill',
                'is_deletable' => 1,
                'created_at' => '2017-12-27 06:04:06',
                'updated_at' => '2017-12-27 06:04:06',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'type' => 'STC Client',
                'is_deletable' => 1,
                'created_at' => '2017-12-27 06:04:13',
                'updated_at' => '2017-12-27 06:04:13',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'type' => 'Multiple Fill',
                'is_deletable' => 0,
                'created_at' => '2017-12-27 06:04:13',
                'updated_at' => '2017-12-27 06:04:13',
                'deleted_at' => null,
            ),
        ));

    }
}
