<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class SecurityClearanceLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('security_clearance_lookups')->delete();

        \DB::table('security_clearance_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'security_clearance' => 'No Clearance',
                'created_at' => '2018-04-26 02:24:32',
                'updated_at' => '2018-04-26 02:25:18',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'security_clearance' => 'Enhanced Reliability',
                'created_at' => '2018-04-26 02:25:39',
                'updated_at' => '2018-04-26 02:25:39',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'security_clearance' => 'Secret',
                'created_at' => '2018-04-26 02:25:49',
                'updated_at' => '2018-04-26 02:25:49',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'security_clearance' => 'Top Secret',
                'created_at' => '2018-04-26 02:26:01',
                'updated_at' => '2018-04-26 02:26:01',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'security_clearance' => 'Secret With Limits',
                'created_at' => '2018-07-13 02:26:01',
                'updated_at' => '2018-07-13 02:26:01',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'security_clearance' => 'Vulnerable Sector Check',
                'created_at' => '2018-07-13 02:26:01',
                'updated_at' => '2018-07-13 02:26:01',
                'deleted_at' => null,
            ),
        ));

    }
}
