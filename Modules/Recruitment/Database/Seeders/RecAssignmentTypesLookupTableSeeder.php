<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecAssignmentTypesLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_assignment_types_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_assignment_types_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'type' => 'Part Time',
                'created_at' => '2017-12-27 06:03:59',
                'updated_at' => '2017-12-27 06:03:59',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'type' => 'Holiday Relief',
                'created_at' => '2017-12-27 06:04:06',
                'updated_at' => '2017-12-27 06:04:06',
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'type' => 'Job Share',
                'created_at' => '2017-12-27 06:04:13',
                'updated_at' => '2017-12-27 06:04:13',
                'deleted_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'type' => 'Full Time',
                'created_at' => '2017-12-27 06:04:21',
                'updated_at' => '2017-12-27 06:04:21',
                'deleted_at' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'type' => 'Short Term Contract',
                'created_at' => '2017-12-27 06:04:27',
                'updated_at' => '2017-12-27 06:04:27',
                'deleted_at' => NULL,
            ),
            5 =>
            array (
                'id' => 6,
                'type' => 'Christmas Coverage',
                'created_at' => '2017-12-27 06:04:35',
                'updated_at' => '2017-12-27 06:04:35',
                'deleted_at' => NULL,
            ),
        ));
    }
}
