<?php

namespace Modules\IdsScheduling\Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IdsRemainderEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(67))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 67,
                'email_subject' => 'Commissionaires IDS Appointment Reminder',
                'email_body' => '<p>Hi {receiverFullName},</p><p>This is a reminder that you have scheduled appointment with Commissionaires IDS</p><p> Booking date : {bookingDate} at {bookingTime} </p><p> Location : {location} </p><p> Requested Service : {serviceName} </p><p> Amount : {serviceRate} + Tax </p><p> Please make sure to arrive within your scheduled time slot (no sooner or later to ensure we maintain social distancing guidelines and reduce lobby traffic). ',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),

        ));
    }
}
