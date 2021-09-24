<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCandidateSecurityGuardingExperincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::connection('mysql_rec')->table('rec_candidate_security_guarding_experinces')->delete();

        \DB::connection('mysql_rec')->table('rec_candidate_security_guarding_experinces')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'guard_licence' => 'Yes',
                'start_date_guard_license' => '2018-07-01',
                'start_date_first_aid' => '2018-07-02',
                'start_date_cpr' => '2018-07-03',
                'expiry_guard_license' => '2018-07-04',
                'expiry_first_aid' => '2018-07-05',
                'expiry_cpr' => '2018-07-06',
                'years_security_experience' => 0.0,
                'most_senior_position_held' => '9',
                'positions_experinces' => '{"site_supervisor":"0","shift_leader":"0","foot_patrol":"0","concierge":"0","security_guard":"0","access_control":"0","cctv_operator":"0","mobile_patrols":"0","investigations":"0","loss_prevention_officer":"0","operations":"0","dispatch":"0","other":"0"}',
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));
    }
}
