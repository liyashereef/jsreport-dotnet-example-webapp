<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class TrainingCategoriesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('training_categories')->delete();

        \DB::table('training_categories')->insert(array(
            0 => array(
                'id' => 1,
                'course_category' => 'Personal Development',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'course_category' => 'Leadership',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'course_category' => 'Customer Service',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'course_category' => 'Security Industry',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'course_category' => 'Legal',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'course_category' => 'Regulatory',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'course_category' => 'Crisis Management',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'course_category' => 'Conflict Resolution',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9,
                'course_category' => 'Note Taking',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'course_category' => 'Site Training',
                'created_at' => '2018-09-25 15:58:00',
                'updated_at' => '2018-09-25 15:58:00',
                'deleted_at' => null,
            ),
        ));

    }
}
