<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmployeeFeedbackEmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 56,
                'email_subject' => 'Employee Feedback raised',
                'email_body' => '<p>Hi ,</p><p>An Employee feedback has been raised against project {projectNumber} - {client} </p>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => null,
            )
        ));
    }
}
