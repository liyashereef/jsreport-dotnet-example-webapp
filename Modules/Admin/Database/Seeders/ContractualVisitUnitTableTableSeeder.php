<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;


class ContractualVisitUnitTableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('contractual_visit_unit_lookups')->delete();
        
        \DB::table('contractual_visit_unit_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'value' => 'Per Week',
                'created_at' => '2019-10-22 06:05:41',
                'updated_at' => '2019-10-22 06:05:41',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'value' => 'Per Month',
                'created_at' => '2019-10-22 06:05:41',
                'updated_at' => '2019-10-22 06:05:41',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'value' => 'Per Quarter',
                'created_at' => '2019-10-22 06:05:41',
                'updated_at' => '2019-10-22 06:05:41',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'value' => 'Per Year',
                'created_at' => '2019-10-22 06:05:41',
                'updated_at' => '2019-10-22 06:05:41',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'value' => 'Per Shift',
                'created_at' => '2019-10-22 06:05:41',
                'updated_at' => '2019-10-22 06:05:41',
                'deleted_at' => NULL,
            )
        ));
        
        
    }
}
