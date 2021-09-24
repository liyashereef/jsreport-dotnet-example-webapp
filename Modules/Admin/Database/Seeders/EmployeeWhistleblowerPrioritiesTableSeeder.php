<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeWhistleblowerPrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('employee_whistleblower_priorities')->delete();
        \DB::table('employee_whistleblower_priorities')->insert(array(
            0 => array(
                'id' => 1,
                'priority' => 'High',
                'rank' => 1,
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            1 => array(
                'id' => 2,
                'priority' => 'Low',
                'rank' => 2,
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            ),
            2 => array(
                'id' => 3,
                'roles' => 'Medium',
                'shortname' => 3,
                'created_at' => '2018-01-03 18:30:00',
                'updated_at' => '2018-01-03 18:30:00',
                'deleted_at' => null,
            )
            
           
       
        ));
    }
}
