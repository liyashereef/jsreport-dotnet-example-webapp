<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class ExperienceLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('experience_lookups')->delete();
        
        \DB::table('experience_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'experience' => 'Customer Service',
                'created_at' => '2017-12-27 06:06:28',
                'updated_at' => '2017-12-27 06:06:28',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'experience' => 'Leadership',
                'created_at' => '2017-12-27 06:06:35',
                'updated_at' => '2017-12-27 06:06:35',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'experience' => 'Problem Solving',
                'created_at' => '2017-12-27 06:06:42',
                'updated_at' => '2017-12-27 06:06:42',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'experience' => 'Weapons',
                'created_at' => '2017-12-27 06:06:48',
                'updated_at' => '2017-12-27 06:06:48',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'experience' => 'Fire Fighting Skill',
                'created_at' => '2017-12-27 06:06:56',
                'updated_at' => '2017-12-27 06:06:56',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
            'experience' => 'French Language (Bilingual)',
                'created_at' => '2017-12-27 06:07:03',
                'updated_at' => '2017-12-27 06:07:03',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}