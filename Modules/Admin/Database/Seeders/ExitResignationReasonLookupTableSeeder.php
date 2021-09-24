<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;


class ExitResignationReasonLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    

        // $this->call("OthersTableSeeder");

        \DB::table('exit_resignation_reason_lookups')->delete();
        \DB::table('exit_resignation_reason_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'reason' => 'Found another job at higher wage',
                'shortname' => 'FAJ',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'reason' => 'Work related stress',
                'shortname' => 'WRS',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'reason' => 'Did not like supervisor',
                'shortname' => 'DLS',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'reason' => 'Did not like client',
                'shortname' => 'DLC',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'reason' => 'Wanted carrier oppurtunity',
                'shortname' => 'WCO',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            )
       
        ));

    }
}
