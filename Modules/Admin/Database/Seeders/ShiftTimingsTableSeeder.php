<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class ShiftTimingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('shift_timings')->delete();

        \DB::table('shift_timings')->insert(array(
            0 => array(
                'id' => 1,
                'shift_name' => 'all',
                'display_name' => 'all',
                'from' => null,
                'to' => null,
                'displayable' => 0,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'shift_name' => 'days',
                'display_name' => 'Days',
                'from' => null,
                'to' => null,
                'displayable' => 1,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'shift_name' => 'afternoons',
                'display_name' => 'Afternoons',
                'from' => null,
                'to' => null,
                'displayable' => 1,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'shift_name' => 'evenings',
                'display_name' => 'Evenings',
                'from' => null,
                'to' => null,
                'displayable' => 1,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'shift_name' => 'overnight',
                'display_name' => 'Overnight',
                'from' => null,
                'to' => null,
                'displayable' => 0,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'shift_name' => 'statutory_holidays',
                'display_name' => 'Statutory holidays',
                'from' => null,
                'to' => null,
                'displayable' => 0,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'shift_name' => 'continental_(12_hours_shift)',
                'display_name' => 'Continental (12 hours shift)',
                'from' => null,
                'to' => null,
                'displayable' => 0,
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
        ));
    }
}
