<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RfpAwardDatesLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('rfp_award_dates')->delete();
        
        \DB::table('rfp_award_dates')->insert(array (
            0 => 
            array (
                'id' => 1,
            	'award_dates' => 10,
                'created_at' => '2019-08-30 00:00:00',
                'updated_at' => '2019-08-30 00:00:00',
                'deleted_at' => NULL,
            ),
        ));

        // $this->call("OthersTableSeeder");
    }
}
