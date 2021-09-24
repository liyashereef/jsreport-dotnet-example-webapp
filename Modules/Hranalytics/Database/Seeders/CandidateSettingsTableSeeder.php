<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('candidate_settings')->delete();

        \DB::table('candidate_settings')->insert(array(
            0 => array(
                'id' => 1,
                'generic_password' => 'job123',
                'encrypted_password' => '$2y$10$9cW9qcTxyxLir7gq/3qbt.9EZ/244T/2EBtIaP8nKQH55ae9dV2Tm',
                'active' => 1,
                'created_at' => '2018-01-03 11:06:00',
                'updated_at' => '2018-01-03 11:06:00',
            ),
        ));

    }
}
