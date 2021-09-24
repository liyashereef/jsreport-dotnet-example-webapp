<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimeSheetApprovalEmailAlertTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(68))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 68,
                'email_subject' => 'Timesheet Approval Email Alert for Employee',
                'email_body' =>'<p>Hello {receiverFullName},</p><p>{customerDetails} timesheet of {payperiod} has been approved by {approvedBy} on {approvedDate}.</p>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),

        ));
    }
}
