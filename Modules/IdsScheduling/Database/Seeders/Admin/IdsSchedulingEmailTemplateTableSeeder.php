<?php

namespace Modules\IdsScheduling\Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IdsSchedulingEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(32, 33, 34))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 32,
                'email_subject' => 'Commissionaires IDS Appointment',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Thank you for scheduling your appointment with Commissionaires IDS</p><p> Booking date : {bookingDate} at {bookingTime} </p><p> Location : {location} </p><p> Requested Service : {serviceName} </p><p> Amount : {serviceRate} + Tax </p><p> Please make sure to arrive within your scheduled time slot (no sooner or later to ensure we maintain social distancing guidelines and reduce lobby traffic).If you need to cancel for any reason, please call {officePhoneNumber} </p><p> If you are exhibiting any symptoms of COVID 19, please limit the spread and self-isolate. For your safety and the safety of our employees, you will be required to pass a health screen and fever scan upon arrival.  </p><p> For further details such as public transport, main intersection and parking, please visit our website  <a href="https://secture360.ca.ca/contact_us" target="_blank"> https://secture360.ca.ca/contact_us </a> </p><br/><h5 > Please Note </h5><ul><li>Applicants for Federal Security Clearances must have the correct paperwork/ORI from their employer otherwise fingerprints cannot be submitted.</li><li>Clients must present two pieces of government issued ID for processing. At least one ID must contain a photo.</li><li>All ID must be valid</li><li> All ID must be original. If it is not original, it can be a certified copy,valid and must be translated in English with one ID containing a photo. </li><li>We do not accept SIN card or red & white health cards.We accept green health cards as a second piece of ID only not as a primary.</li><li> Upon arrival, you will be screened for fever. If your body temperature is 37.6°C (99.7°F) or higher, you will not be granted service and will be turned away from the site. </li><li> Over the phone service will not be provided. </li><li> Starting Jan 01, 2021, a surcharge of $2.50 will be applied to all services to cover additional costs incurred during the pandemic related to automated scheduling and PPE.</li><li>Please note, a $10 surcharge will be added to your invoice for no-shows or any cancellation with less than 2 hours notice.</li></ul> ',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 33,
                'email_subject' => 'Your {serviceName} Appointment has been Rescheduled',
                'email_body' => '<p>Hi {receiverFullName},</p><p> As per your request, we are letting you know that your {serviceName} appointment has been rescheduled.  Please see the details below. </p><p> Booking date : {bookingDate} at {bookingTime} </p><p> Location : {location} </p><p> Amount : {serviceRate} + Tax </p><p> Please make sure to arrive within your scheduled time slot (no sooner or later to ensure we maintain social distancing guidelines and reduce lobby traffic).If you need to cancel for any reason, please call {officePhoneNumber} </p><p> If you are exhibiting any symptoms of COVID 19, please limit the spread and self-isolate. For your safety and the safety of our employees, you will be required to pass a health screen and fever scan upon arrival.  </p><p> For further details such as public transport, main intersection and parking, please visit our website  <a href="https://secture360.ca.ca/contact_us" target="_blank"> https://secture360.ca.ca/contact_us </a> </p><br/><h5 > Please Note </h5><ul><li>Applicants for Federal Security Clearances must have the correct paperwork/ORI from their employer otherwise fingerprints cannot be submitted.</li><li>Clients must present two pieces of government issued ID for processing. At least one ID must contain a photo.</li><li>All ID must be valid</li><li> All ID must be original. If it is not original, it can be a certified copy,valid and must be translated in English with one ID containing a photo. </li><li>We do not accept SIN card or red & white health cards.We accept green health cards as a second piece of ID only not as a primary.</li><li> Upon arrival, you will be screened for fever. If your body temperature is 37.6°C (99.7°F) or higher, you will not be granted service and will be turned away from the site. </li><li> Over the phone service will not be provided. </li><li> Starting Jan 01, 2021, a surcharge of $2.50 will be applied to all services to cover additional costs incurred during the pandemic related to automated scheduling and PPE.</li><li>Please note, a $10 surcharge will be added to your invoice for no-shows or any cancellation with less than 2 hours notice.</li></ul> ',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 34,
                'email_subject' => 'Your {serviceName} Appointment has been cancelled',
                'email_body' => '<p>Hi {receiverFullName},</p><p> As per your request we are cancelling your appointment with Commissionaires IDS </p><p> Booking date : {bookingDate} at {bookingTime} </p><p> Canceling date : {cancelingDate} at {cancelingTime} </p><p> This is a gentle remainder, that your appointment with Commissionaires IDS has been Canceled based on your request. </p><p> For further details please call {officePhoneNumber} or visit our website  <a href="https://secture360.ca.ca/contact_us" target="_blank"> https://secture360.ca.ca/contact_us </a> </p><br/>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),

        ));
    }
}
