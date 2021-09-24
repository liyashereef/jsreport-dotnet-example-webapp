<?php

namespace Modules\Osgc\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OsgcEmailTemplateSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(
           52, 53, 54
        ))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 52,
                'email_subject' => 'User Registration',
                'email_body' => '<p>Hi {receiverFullName},</p><p><a href="{activationUrl}" target="_blank">Please click the link to activate your account</a></p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 53,
                'email_subject' => 'Reset Password',
                'email_body' => '<p>Hello {receiverFullName},</p><p>You password has been changed and new password is {randomPassword}</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 54,
                'email_subject' => 'Course Certificate',
                'email_body' => '<p>Hello {receiverFullName},</p><p>Your course ( {courseName} ) has been completed.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => NULL,
                ),
        ));
    }
}
