<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UniformSchedulingEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(29,30,31))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 29,
                'email_subject' => 'Commissionaires uniform appointment scheduled',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Thank you for scheduling your appointment with Commissionaires</p><p>Date : {bookedDate}</p><p>Location : {officeNameAndAddress}.<p>Please make sure to arrive within your scheduled time slot (no sooner or later to ensure we maintain social distancing guidelines and reduce lobby traffic).</p><p>If you need to cancel for any reason, please call {officePhoneNumber}</p>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 30,
                'email_subject' => 'Commissionaires uniform appointment has been rescheduled',
                'email_body' => '<p>Hi {receiverFullName},</p><p>As per your request, we are letting you know that your appointment has been rescheduled.Please see the details below.</p><p>Date : {bookedDate}</p><p>Location : {officeNameAndAddress}.<p>Please make sure to arrive within your scheduled time slot (no sooner or later to ensure we maintain social distancing guidelines and reduce lobby traffic).</p><p>If you need to cancel for any reason, please call {officePhoneNumber}</p>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 35,
                'email_subject' => 'Commissionaires uniform scheduling info',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Booking Details</p><p> Scheduled User Name : {scheduledUserName}</p><p> Phone Number : {phoneNumber}</p><p>Date : {bookedDate}</p>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            )
        ));
    }

}
