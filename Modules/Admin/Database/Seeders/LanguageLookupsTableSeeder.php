<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class LanguageLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('language_lookups')->delete();
        
        \DB::table('language_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'language' => 'English',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'language' => 'French',
                'created_at' => '2017-12-29 06:28:47',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}