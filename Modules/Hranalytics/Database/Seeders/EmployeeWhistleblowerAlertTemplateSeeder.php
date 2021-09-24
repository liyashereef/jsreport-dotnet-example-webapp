<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeWhistleblowerAlertTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(
           55
        ))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 55,
                'email_subject' => 'Employee Whistle Blower Alert Email',
                'email_body' => '<p>Hi {receiverFullName},</p><p>This is an employee whistle blower alert email raised aginst your Contract {contractName}</p><p>Subject - {subject}</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),

        ));
    }
}
