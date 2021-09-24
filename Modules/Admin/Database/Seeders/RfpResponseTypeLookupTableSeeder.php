<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class RfpAwardDatesLookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        \DB::table('rfp_response_type_lookups')->delete();
        \DB::table('rfp_response_type_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
            	'rfp_response_type' => 'New RFP',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime()
            ),
        ));
    }
}
