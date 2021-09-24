<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateScheduleEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(23, 24, 25, 28))->delete();

        \DB::table('email_templates')->insert(array(
            0 => array(
                'type_id' => 23,
                'email_subject' => 'Candidate Schedule Shift Status Update',
                'email_body' => '<p>Hello,</p><p>Shift status updated, Please find the below details <br /><br />Assignee Name: {candidateScheduleAssigneeName} <br />Start Date: {candidateScheduleShiftStartDate} <br />Start Time: {candidateScheduleShiftStartTime} <br />End Date: {candidateScheduleShiftEndDate} <br/>End Time: {candidateScheduleShiftEndTime} <br/>Status: {candidateScheduleShiftStatus} <br />Notes: {candidateScheduleShiftStatusNote} <br />Site Rate/Accepted Rate: ${candidateScheduleSiteRate} <br />Number of Positions: {candidateScheduleNoOfShifts} <br />Shift Timing: {candidateScheduleShiftTiming} <br />Site Address: {candidateScheduleSiteAddress} <br />Site City: {candidateScheduleCity} <br />Postal Code: {candidateSchedulePostalCode} <br />Assignment Type: {candidateScheduleAssignmentType}</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            1 => array(
                'type_id' => 24,
                'email_subject' => 'Candidate Schedule Employee Unassigned',
                'email_body' => '<p>Hello,</p><p>Shit has been Unassigned, Please find the below details <br /><br />Assigned Person: {candidateScheduleAssigneeName} <br />Start Date: {candidateScheduleShiftStartDate} <br />Start Time: {candidateScheduleShiftStartTime} <br />End Date: {candidateScheduleShiftEndDate} <br/>End Time: {candidateScheduleShiftEndTime} <br />Site Rate/Accepted Rate: ${candidateScheduleSiteRate} <br />Number of Positions: {candidateScheduleNoOfShifts} <br />Shift Timing: {candidateScheduleShiftTiming} <br />Site Address: {candidateScheduleSiteAddress} <br />Site City: {candidateScheduleCity} <br />Postal Code: {candidateSchedulePostalCode} <br />Assignment Type: {candidateScheduleAssignmentType}</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            2 => array(
                'type_id' => 25,
                'email_subject' => 'Candidate Schedule Shift Removed',
                'email_body' => '<p>Hello,</p><p>Shit has been Removed, Please find the below details <br /><br />Assigned Person: {candidateScheduleAssigneeName} <br />Start Date: {candidateScheduleShiftStartDate} <br />Start Time: {candidateScheduleShiftStartTime} <br />End Date: {candidateScheduleShiftEndDate} <br/>End Time: {candidateScheduleShiftEndTime} <br />Site Rate/Accepted Rate: ${candidateScheduleSiteRate} <br />Number of Positions: {candidateScheduleNoOfShifts} <br />Shift Timing: {candidateScheduleShiftTiming} <br />Site Address: {candidateScheduleSiteAddress} <br />Site City: {candidateScheduleCity} <br />Postal Code: {candidateSchedulePostalCode} <br />Assignment Type: {candidateScheduleAssignmentType}</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
            3 => array(
                'type_id' => 28,
                'email_subject' => 'Candidate Open Shift Notification',
                'email_body' => '<p>Hello,</p><p>Shit has been Submitted, Please find the below details <br /><br />Submitted Person: {candidateScheduleAssigneeName} <br />Start Date: {candidateScheduleShiftStartDate} <br />Start Time: {candidateScheduleShiftStartTime} <br />End Date: {candidateScheduleShiftEndDate} <br/>End Time: {candidateScheduleShiftEndTime} <br />Site Rate/Accepted Rate: ${candidateScheduleSiteRate} <br />Number of Positions: {candidateScheduleNoOfShifts} <br />Shift Timing: {candidateScheduleShiftTiming} <br />Site Address: {candidateScheduleSiteAddress} <br />Site City: {candidateScheduleCity} <br />Postal Code: {candidateSchedulePostalCode} <br />Assignment Type: {candidateScheduleAssignmentType}</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => null,
            ),
        ));
    }
}
