<?php

namespace Modules\IdsScheduling\Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IdsSchedulingEmailNotificationHelpersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_notification_helpers')->whereIn('email_notification_type_id', array(57,58))->delete();
        \DB::table('email_notification_helpers')->insert(array(
            0 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{bookingDate}',
                'replace_value' => 'Booking Date',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            1 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{bookingTime}',
                'replace_value' => 'Booking Time',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            2 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{location}',
                'replace_value' => 'Location',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            3 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{serviceName}',
                'replace_value' => 'Service Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            4 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{serviceRate}',
                'replace_value' => 'Service Rate',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            5 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{officePhoneNumber}',
                'replace_value' => 'Office Phone number',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            6 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{refundRate}',
                'replace_value' => 'Refund Rate',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            7 => array(
               'email_notification_type_id' => 57,
               'replace_string' => '{clientFullName}',
               'replace_value' => 'Client Full Name',
               'created_at' => \Carbon::now(),
               'updated_at' => \Carbon::now(),
               'deleted_at' => null,
            ),
            8 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{email}',
                'replace_value' => 'Client Email',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            9 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{phoneNumber}',
                'replace_value' => 'Client Phone Number',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            10 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{refundInitiatedBy}',
                'replace_value' => 'Refund Initiated By',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            11 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{receiverFullName}',
                'replace_value' => 'Receiver Full Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
             ),
             12 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{paymentId}',
                'replace_value' => 'Payment Id',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
             ),

             13 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{serviceName}',
                'replace_value' => 'Service Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
             ),
             14 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{receiverFullName}',
                'replace_value' => 'Receiver Full Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
             ),
             15 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{bookingDate}',
                'replace_value' => 'Booking Date',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            16 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{bookingTime}',
                'replace_value' => 'Booking Time',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            17 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{cancelingDate}',
                'replace_value' => 'Cancel Date',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            18 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{cancelingTime}',
                'replace_value' => 'Cancel Time',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            19 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{officePhoneNumber}',
                'replace_value' => 'Office Phone number',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            20 => array(
                'email_notification_type_id' => 58,
                'replace_string' => '{location}',
                'replace_value' => 'Location',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),

            21 => array(
                'email_notification_type_id' => 34,
                'replace_string' => '{bookingDate}',
                'replace_value' => 'Booking Date',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            22 => array(
                'email_notification_type_id' => 34,
                'replace_string' => '{bookingTime}',
                'replace_value' => 'Booking Time',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            23 => array(
                'email_notification_type_id' => 34,
                'replace_string' => '{cancelingDate}',
                'replace_value' => 'Cancel Date',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            24 => array(
                'email_notification_type_id' => 34,
                'replace_string' => '{cancelingTime}',
                'replace_value' => 'Cancel Time',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            25 => array(
                'email_notification_type_id' => 34,
                'replace_string' => '{officePhoneNumber}',
                'replace_value' => 'Office Phone number',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            26 => array(
                'email_notification_type_id' => 34,
                'replace_string' => '{serviceName}',
                'replace_value' => 'Service Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            27 => array(
                'email_notification_type_id' => 33,
                'replace_string' => '{serviceName}',
                'replace_value' => 'Service Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            28 => array(
                'email_notification_type_id' => 33,
                'replace_string' => '{serviceRate}',
                'replace_value' => 'Service Rate',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            28 => array(
                'email_notification_type_id' => 57,
                'replace_string' => '{onlinePaid}',
                'replace_value' => 'Online Paid Amount',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            )

        ));
    }
}
