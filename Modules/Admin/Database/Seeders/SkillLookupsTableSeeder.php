<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class SkillLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('skill_lookups')->delete();

        \DB::table('skill_lookups')->insert(array(
            0 => array(
                'id' => 1,
                'category' => 'Special Skills',
                'skills' => 'Microsoft Word',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'category' => 'Special Skills',
                'skills' => 'Microsoft Excel',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'category' => 'Special Skills',
                'skills' => 'Microsoft Powerpoint',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'category' => 'Soft Skills',
                'skills' => 'Customer Service',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'category' => 'Soft Skills',
                'skills' => 'Leadership',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'category' => 'Soft Skills',
                'skills' => 'Problem Solving And Critical Thinking',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'category' => 'Soft Skills',
                'skills' => 'Time Management',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ),

        ));

    }
}
