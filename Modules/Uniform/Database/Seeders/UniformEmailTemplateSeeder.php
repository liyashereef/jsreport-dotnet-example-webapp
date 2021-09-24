<?php

namespace Modules\Uniform\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\EmailTemplate;

class UniformEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $uniformOrderTypes = EmailNotificationType::whereIn("type", [
            "uniform_order_returned",
            "uniform_order_received",
            "uniform_order_shipped",
            "uniform_order_cancelled",
            "uniform_order_delivered",
            "uniform_ordered"

        ])->pluck("id")->toArray();
        if (count($uniformOrderTypes) > 0) {
            EmailTemplate::whereIn('type_id', $uniformOrderTypes)->delete();
        }

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 65,
                'email_subject' => 'Uniform Ordered',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Uniform ordered successfully </p><p></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => '2021-06-22 06:08:04',
            ),
            1 =>
            array(
                'type_id' => 61,
                'email_subject' => 'Uniform Order Received',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Uniform order received successfully</p><p></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 62,
                'email_subject' => 'Uniform Order Shipped',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Uniform order shipped successfully</p><p></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'type_id' => 63,
                'email_subject' => 'Uniform Order Cancelled',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Uniform order cancelled successfully</p><p></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'type_id' => 64,
                'email_subject' => 'Uniform Order Delivered',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Uniform order delivered successfully</p><p></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'type_id' => 60,
                'email_subject' => 'Uniform Order Returned',
                'email_body' => '<p>Hi {receiverFullName},</p><p>Uniform order returned successfully</p><p></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),

        ));
    }
}
