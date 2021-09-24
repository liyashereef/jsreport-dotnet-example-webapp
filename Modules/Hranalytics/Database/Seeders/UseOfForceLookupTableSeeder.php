<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UseOfForceLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('use_of_force_lookups')->delete();

        \DB::table('use_of_force_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'use_of_force' => 'UOF level 1',
                'order_sequence' => 1,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'use_of_force' => 'UOF level 2 with Handcuff',
                'order_sequence' => 2,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'use_of_force' => 'UOF level 2 with Handcuff and Batton',
                'order_sequence' => 3,
                'created_at' => '2019-03-21 15:58:00',
                'updated_at' => '2019-03-21 15:58:00',
                'deleted_at' => null,
            ),

        ));
    }
}
