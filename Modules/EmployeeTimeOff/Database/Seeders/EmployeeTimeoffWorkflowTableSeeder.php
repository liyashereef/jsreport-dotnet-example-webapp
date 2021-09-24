<?php

namespace Modules\EmployeeTimeOff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimeoffWorkflowTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        \DB::table('employee_timeoff_workflow')->delete();
        
        \DB::table('employee_timeoff_workflow')->insert(array (
            0 => array(
                'id' => 1,
                'emp_role_id' => 7,
                'level' => '1',
                'approver_role_id' => 9,
                'email_notification' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s')
            ),
            1 => array(
                'id' => 2,
                'emp_role_id' => 7,
                'level' => '2',
                'approver_role_id' => 5,
                'email_notification' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s')
            ),
            2 => array(
                'id' => 3,
                'emp_role_id' => 6,
                'level' => '1',
                'approver_role_id' => 9,
                'email_notification' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s')
            ),
            3 => array(
                'id' => 4,
                'emp_role_id' => 6,
                'level' => '2',
                'approver_role_id' => 5,
                'email_notification' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('y_m-d H:i:s')
            ),
        ));
    }
}
