<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class ShiftModuleFieldTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('shift_module_field_types')->delete();
        \DB::table('shift_module_field_types')->insert([

            0 => [
                'id' => 1,
                'type_name' => 'Picture',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            1 => [
                'id' => 2,
                'type_name' => 'Location',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            2 => [
                'id' => 3,
                'type_name' => 'Dropdown',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            3 => [
                'id' => 4,
                'type_name' => 'Notes',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            4 => [
                'id' => 5,
                'type_name' => 'Video',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],


            5 => [
                'id' => 6,
                'type_name' => 'Dropdown with Info',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],

            6 => [
                'id' => 7,
                'type_name' => 'Text Without Mic',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],
            7 => [
                'id' => 8,
                'type_name' => 'Post Order',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'deleted_at' => null,
            ],



        ]);

    }
}
