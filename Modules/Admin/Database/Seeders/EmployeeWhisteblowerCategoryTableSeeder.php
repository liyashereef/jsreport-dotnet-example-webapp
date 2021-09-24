<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeWhisteblowerCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        \DB::table('employee_whistleblower_categories')->delete();
        \DB::table('employee_whistleblower_categories')->insert(array(
            0 => array(
                'id' => 1,
                'roles' => 'Report Fraud',
                'shortname' => 'RF',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,  
                'roles' => 'Report Harrassement',
                'shortname' => 'RH',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'roles' => 'Report Unethical Behaviour',
                'shortname' => 'RUB',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            3 => array(
                'id' => 4,
                'roles' => 'Report Vilolation of policy',
                'shortname' => 'RVP',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            4 => array(
                'id' => 5,
                'roles' => 'Report Timesheet Concerns',
                'shortname' => 'RTC',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            5 => array(
                'id' => 6,
                'roles' => 'Report Concerns With Client',
                'shortname' => 'RCC',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            6 => array(
                'id' => 7,
                'roles' => 'Report Concerns With Supervisor',
                'shortname' => 'RCS',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            7 => array(
                'id' => 8,
                'roles' => 'Report Concerns With Management',
                'shortname' => 'RCM',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            8 => array(
                'id' => 9   ,
                'roles' => 'Report Safety Concern',
                'shortname' => 'RSC',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            9 => array(
                'id' => 10,
                'roles' => 'General Feedback',
                'shortname' => 'GF',
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            )
       
        ));
    }
}
