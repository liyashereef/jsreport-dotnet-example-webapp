<?php

namespace Modules\Timetracker\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class QrPatrolEmailTemplateTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->whereIn('type_id', array(59))->delete();

        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 59,
                'email_subject' => 'Daily Activity Report (Qr patrol)',
                'email_body' => '<p>Hi,</p><p>Please see the report</p>',
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
                'deleted_at' => NULL,
            ),
            
        ));
    }
}
