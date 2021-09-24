<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class LeaveReasonsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('leave_reasons')->delete();

        \DB::table('leave_reasons')->insert(array(
            // 0 => array(
            //     'id' => 1,
            //     'reason' => 'Other',
            //     'created_at' => '2017-12-27 06:02:14',
            //     'updated_at' => '2017-12-27 06:02:14',
            //     'deleted_at' => null,
            // ),
            0 => array(
                'id' => 1,
                'reason' => 'No Reason',
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'reason' => 'Personal',
                'created_at' => '2017-12-27 06:02:32',
                'updated_at' => '2017-12-27 06:02:32',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'reason' => 'Sick',
                'created_at' => '2017-12-27 06:02:42',
                'updated_at' => '2017-12-27 06:02:42',
                'deleted_at' => null,
            ),
        ));

    }
}
