<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class PositionLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('position_lookups')->delete();

        \DB::table('position_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'position' => 'Site Supervisor',
                'created_at' => '2017-12-27 06:02:14',
                'updated_at' => '2017-12-27 06:02:14',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'position' => 'Shift Leader',
                'created_at' => '2017-12-27 06:02:22',
                'updated_at' => '2017-12-27 06:02:22',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'position' => 'Foot Patrol',
                'created_at' => '2017-12-27 06:02:32',
                'updated_at' => '2017-12-27 06:02:32',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'position' => 'Concierge',
                'created_at' => '2017-12-27 06:02:42',
                'updated_at' => '2017-12-27 06:02:42',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'position' => 'Security Guard',
                'created_at' => '2017-12-27 06:02:50',
                'updated_at' => '2017-12-27 06:02:50',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'position' => 'Access Control',
                'created_at' => '2017-12-27 06:02:57',
                'updated_at' => '2017-12-27 06:02:57',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'position' => 'CCTV Operator',
                'created_at' => '2017-12-27 06:03:04',
                'updated_at' => '2017-12-27 06:03:04',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'position' => 'Mobile Patrols',
                'created_at' => '2017-12-27 06:03:11',
                'updated_at' => '2017-12-27 06:03:11',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'position' => 'Investigations',
                'created_at' => '2017-12-27 06:03:19',
                'updated_at' => '2017-12-27 06:03:19',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'position' => 'Loss Prevention Officer',
                'created_at' => '2017-12-27 06:03:19',
                'updated_at' => '2017-12-27 06:03:19',
                'deleted_at' => null,
            ),
            10 => array(
                'id' => 11,
                'position' => 'Operations',
                'created_at' => '2017-12-27 06:03:19',
                'updated_at' => '2017-12-27 06:03:19',
                'deleted_at' => null,
            ),
            11 => array(
                'id' => 12,
                'position' => 'Dispatch',
                'created_at' => '2017-12-27 06:03:19',
                'updated_at' => '2017-12-27 06:03:19',
                'deleted_at' => null,
            ),
        ));

    }
}
