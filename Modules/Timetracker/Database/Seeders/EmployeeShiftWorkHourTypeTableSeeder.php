<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeShiftWorkHourTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::table('employee_shift_work_hour_types')->delete();

        \DB::table('employee_shift_work_hour_types')->insert([
            [
             'id'=>1,
             "name" => "Regular Hours",
             'description'=>'Regular Hours',
             'is_editable'=>0,
             'is_deletable'=>0,
             'created_by'=>1,
             'updated_by'=>1,
             'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
             'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
            ['id'=>2,
             "name" => "Overtime Hours",
             'description'=>'Regular Hours',
             'is_editable'=>0,
             'is_deletable'=>0,
             'created_by'=>1,
             'updated_by'=>1,
             'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
             'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
            'id'=>3,
            "name" => "Statutory Hours",
            'description'=>'Regular Hours',
            'is_editable'=>0,
            'is_deletable'=>0,
            'created_by'=>1,
            'updated_by'=>1,
            'created_at' => \Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => \Carbon::now()->format('Y-m-d H:i:s')]
        ]);
    }
}
