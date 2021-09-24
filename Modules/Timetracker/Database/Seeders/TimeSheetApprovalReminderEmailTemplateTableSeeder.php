<?php

namespace Modules\Timetracker\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TimeSheetApprovalReminderEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(19, 20, 21))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 19,
                'email_subject' => 'Time Sheet Approval Reminder 1',
                'email_body' => '<p>Hi {employeeName} {employeeNumber},</p><p>This is a gentle reminder that {payperiodDetails} is going to expire on {deadlineDate}  {deadlineTime} . Please approve it before it expire.</p> <p>Regards,<br />{clientDetails} &nbsp;</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 20,
                'email_subject' => 'Time Sheet Approval Reminder 2',
                'email_body' => '<p>Hi {employeeName} {employeeNumber},</p><p>This is a gentle reminder that {payperiodDetails} is going to expire on {deadlineDate}  {deadlineTime} . Please approve it before it expire.</p> <p>Regards,<br />{clientDetails} &nbsp;</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 21,
                'email_subject' => 'Time Sheet Approval Reminder 3',
                'email_body' => '<p>Hi {employeeName} {employeeNumber},</p><p>This is a gentle reminder that {payperiodDetails} is going to expire on {deadlineDate}  {deadlineTime} . Please approve it before it expire.</p> <p>Regards,<br />{clientDetails} &nbsp;</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            )
        ));
    }
}
