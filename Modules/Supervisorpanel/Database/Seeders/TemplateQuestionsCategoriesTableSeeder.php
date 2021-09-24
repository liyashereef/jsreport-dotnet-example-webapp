<?php

namespace Modules\Supervisorpanel\Database\Seeders;

use Illuminate\Database\Seeder;

class TemplateQuestionsCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('template_questions_categories')->delete();
        
        \DB::table('template_questions_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'description' => 'Site',
                'average' => 'No',
                'created_at' => '2018-03-13 00:00:00',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'description' => 'Scope',
                'average' => 'Yes',
                'created_at' => '2018-03-13 00:00:00',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'description' => 'Training',
                'average' => 'Yes',
                'created_at' => '2018-03-13 11:34:34',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'description' => 'Employee',
                'average' => 'Yes',
                'created_at' => '2018-03-13 11:35:01',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'description' => 'Client',
                'average' => 'Yes',
                'created_at' => '2018-03-13 11:35:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'description' => 'Schedule',
                'average' => 'Yes',
                'created_at' => '2018-03-13 11:35:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'description' => 'Coverage',
                'average' => 'Yes',
                'created_at' => '2018-03-13 11:35:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'description' => 'Turnover',
                'average' => 'Yes',
                'created_at' => '2018-03-13 11:35:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}