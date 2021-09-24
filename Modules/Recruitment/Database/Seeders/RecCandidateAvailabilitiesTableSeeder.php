<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCandidateAvailabilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::connection('mysql_rec')->table('rec_candidate_availabilities')->delete();

        \DB::connection('mysql_rec')->table('rec_candidate_availabilities')->insert(array(
            0 => array(
                'id' => 1,
                'candidate_id' => 1,
                'current_availability' => 'Full-Time (Around 40 hours per week)',
                'days_required' => '["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]',
                'shifts' => '["Days","Afternoons","Evenings","Overnight","Statutory holidays","Continental (12 Hours Shift)"]',
                'availability_explanation' => null,
                'availability_start' => '2018-07-26',
                'understand_shift_availability' => 'Yes',
                'available_shift_work' => 'Yes',
                'explanation_restrictions' => null,
                'created_at' => '2018-07-25 12:52:16',
                'updated_at' => '2018-07-25 12:52:16',
            ),
        ));
    }
}
