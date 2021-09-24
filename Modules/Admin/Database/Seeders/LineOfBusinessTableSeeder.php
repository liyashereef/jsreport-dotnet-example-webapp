<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class LineOfBusinessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \DB::table('line_of_businesses')->delete();
        
        \DB::table('line_of_businesses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'lineofbusinesstitle' => 'NMSO',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'lineofbusinesstitle' => 'Commercial',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'lineofbusinesstitle' => 'Other',
                'status' => 1,
                'createdby' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            )
        ));
    }
}
