<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('colors')->delete();
        
        \DB::table('colors')->insert([
            'color_name' => 'Red',
            'color_class_name' => 'red',
        ]);
        \DB::table('colors')->insert([
            'color_name' => 'Yellow',
            'color_class_name' => 'yellow',
        ]);
        \DB::table('colors')->insert([
            'color_name' => 'Green',
            'color_class_name' => 'green',
        ]);
        \DB::table('colors')->insert([
            'color_name' => 'Black',
            'color_class_name' => 'black',
        ]);
    }
}
