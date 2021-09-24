<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class TrainingTimingLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('training_timing_lookups')->delete();
        
        \DB::table('training_timing_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
            'training' => 'External Training (Course related)',
                'created_at' => '2017-12-27 06:04:47',
                'updated_at' => '2017-12-27 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'training' => 'OJT',
                'created_at' => '2017-12-27 06:04:54',
                'updated_at' => '2017-12-27 06:04:54',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}