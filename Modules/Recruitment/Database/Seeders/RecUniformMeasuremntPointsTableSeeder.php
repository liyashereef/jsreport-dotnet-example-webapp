<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RecUniformMeasuremntPointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_uniform_measurement_points')->delete();

        \DB::connection('mysql_rec')->table('rec_uniform_measurement_points')->insert(array(
            0 => array(
                'name' => 'Neck',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            1 => array(
                'name' => 'Arm',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
             2 => array(
                'name' => 'Chest',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            3 => array(
                'name' => 'Waist',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            4 => array(
                'name' => 'Hip',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),
            5 => array(
                'name' => 'Inside leg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ),


        ));
    }
}
