<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AddwhistleblowerstatuslookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('whistleblower_status_lookups')->delete();

        DB::table('whistleblower_status_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Open',
                'status' => 1,
                'inital_status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'In Progress',
                'status' => 2,
                'inital_status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'To be tested',
                'status' => 3,
                'inital_status' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            )
        ));


    }
}
