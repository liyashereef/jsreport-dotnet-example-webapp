<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class IncidentAmendmentEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(65))->delete();

        \DB::table('email_templates')->insert(array(
            0 => array(
                'type_id' => 65,
                    'email_subject' => 'New Incident Amendment reported',
                    'email_body' =>'<p>Hello {receiverFullName},</p><p>A new Incident amendment has been reported by {reporterFullName} at {client}-{projectNumber}.</p>',
                    'created_at' => '2019-05-21 06:04:47',
                    'updated_at' => '2019-05-21 06:04:47',
                    'deleted_at' => null,
            )
        ));
    }
}
