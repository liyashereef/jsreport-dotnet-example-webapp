<?php

namespace Modules\LearningAndTraining\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CourseContentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('course_content_types')->delete();
        
        \DB::table('course_content_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Image',
                'active' => 1,
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'PDF',
                'active' => 1,
                'created_at' => '2019-05-21 06:04:54',
                'updated_at' => '2019-05-21 06:04:54',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'Video',
                'active' => 1,
                'created_at' => '2019-05-21 06:04:54',
                'updated_at' => '2019-05-21 06:04:54',
                'deleted_at' => NULL,
            ),
        ));
    }
}
