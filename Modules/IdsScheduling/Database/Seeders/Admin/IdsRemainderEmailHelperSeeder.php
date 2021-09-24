<?php

namespace Modules\IdsScheduling\Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IdsRemainderEmailHelperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_notification_helpers')->whereIn('email_notification_type_id', array(67))->delete();
        \DB::table('email_notification_helpers')->insert(array(
            0 => array(
                'email_notification_type_id' => 67,
                'replace_string' => '{bookingDate}',
                'replace_value' => 'Booking Date',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            1 => array(
                'email_notification_type_id' => 67,
                'replace_string' => '{bookingTime}',
                'replace_value' => 'Booking Time',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            2 => array(
                'email_notification_type_id' => 67,
                'replace_string' => '{location}',
                'replace_value' => 'Location',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            3 => array(
                'email_notification_type_id' => 67,
                'replace_string' => '{serviceName}',
                'replace_value' => 'Service Name',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
            4 => array(
                'email_notification_type_id' => 67,
                'replace_string' => '{serviceRate}',
                'replace_value' => 'Service Rate',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            ),
           

        ));
    }
}
