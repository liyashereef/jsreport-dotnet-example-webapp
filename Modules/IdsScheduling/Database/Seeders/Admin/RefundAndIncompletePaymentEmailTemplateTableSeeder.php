<?php

namespace Modules\IdsScheduling\Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RefundAndIncompletePaymentEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(57,58))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 57,
                'email_subject' => 'Refund Initiated for {serviceName}',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Cancellation of ids scheduling refund amount is {refundRate}</p><p>Customer Details:</p><p> Name : {clientFullName} </p><p> Email : {email} </p><p> Phone number : {phoneNumber} </p><p>Booking Details:</p><p> Booking date : {bookingDate} at {bookingTime} </p><p> Location : {location} </p><p> Requested Service : {serviceName} </p><p> Stripe Key : {paymentId} </p><p> Online Paid Amount : {onlinePaid} </p><p> Total Fee : {serviceRate} </p><p> Refund Amount : {refundRate} </p><p> Refund Date : {refundDate} </p><p> Refund Initiated By : {refundInitiatedBy} </p><p> For further details please call {officePhoneNumber} or visit our website  <a href="https://secture360.ca.ca/contact_us" target="_blank"> https://secture360.ca.ca/contact_us </a> </p><br/>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 58,
                'email_subject' => 'Your {serviceName} Appointment has been cancelled due to incomplete payment',
                'email_body' => '<p>Hi {receiverFullName},</p><p> We are cancelling your appointment with Commissionaires IDS due to incomplete payment </p><p> Booking date : {bookingDate} at {bookingTime} </p><p> Location : {location} </p><p>  Date of Cancellation : {cancelingDate} at {cancelingTime} </p><p> This is a gentle remainder, that your appointment with Commissionaires IDS has been Canceled based on your request. </p><p> For further details please call {officePhoneNumber} or visit our website  <a href="https://secture360.ca.ca/contact_us" target="_blank"> https://secture360.ca.ca/contact_us </a> </p><br/>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
        ));

    }
}
