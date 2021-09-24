<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ShiftTypeTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('shift_types')->delete();

        \DB::table('shift_types')->insert([
            ['id'=>1,"name" => "Regular"],
            ['id'=>2,"name" => "MSP"],
            ['id'=>3,"name" => "MST"]
        ]);
    
    }
    
}
