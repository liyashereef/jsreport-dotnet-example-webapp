<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        \DB::table('days')->delete();
        
        \DB::table('days')->insert(array (
            0 => [
                'id' => 1,
                'name' => 'Monday',
            ],
            1 => [
                'id' => 2,
                'name' => 'Tuesday',
            ],
            2 => [
                'id' => 3,
                'name' => 'Wednesday',
            ],
            3 => [
                'id' => 4,
                'name' => 'Thursday',
            ],
            4 => [
                'id' => 5,
                'name' => 'Friday',
            ],
            5 => [
                'id' => 6,
                'name' => 'Saturday',
            ],
            6 => [
                'id' => 7,
                'name' => 'Sunday',
            ],
        ));

    }
}
