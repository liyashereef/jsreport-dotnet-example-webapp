<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class WorkTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('work_types')->delete();
        
        \DB::table('work_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Permanent',
                'active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'Contract',
                'active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),            
        ));
        
        
    }
}