<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class StatusLogLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('status_log_lookups')->delete();

        \DB::table('status_log_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'status' => 'Called And Accepted Shift',
                'score' => 2,
                'created_at' => '2018-06-20 12:31:59',
                'updated_at' => '2018-06-20 12:31:59',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'status' => 'Called And No Answer',
                'created_at' => '2018-06-20 12:31:06',
                'updated_at' => '2018-06-20 12:31:06',
                'score' => 0,
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'status' => 'Called And Declined Shift',
                'created_at' => '2018-06-20 12:31:13',
                'updated_at' => '2018-06-20 12:31:13',
                'score' => 1,
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'status' => 'Cancelled the Shift',
                'created_at' => '2018-06-20 12:31:13',
                'updated_at' => '2018-06-20 12:31:13',
                'score' => 1,
                'deleted_at' => null,
            ),
        ));

    }
}
