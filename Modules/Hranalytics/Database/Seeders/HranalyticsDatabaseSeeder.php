<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class HranalyticsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(CandidateSettingsTableSeeder::class);
        $this->call(JobsTableSeeder::class);
        $this->call(CandidateAttachmentsTableSeeder::class);
        $this->call(CandidateAvailabilitiesTableSeeder::class);
        $this->call(CandidateEducationsTableSeeder::class);
        $this->call(CandidateEmploymentHistorysTableSeeder::class);
        $this->call(CandidateExperiencesTableSeeder::class);
        $this->call(CandidateJobsTableSeeder::class);
        $this->call(CandidateLanguagesTableSeeder::class);
        $this->call(CandidateMiscellaneousesTableSeeder::class);
        $this->call(CandidateReferencesTableSeeder::class);
        $this->call(CandidateScreeningQuestionsTableSeeder::class);
        $this->call(CandidateSecurityClearancesTableSeeder::class);
        $this->call(CandidateSecurityGuardingExperincesTableSeeder::class);
        $this->call(CandidateSecurityProximitiesTableSeeder::class);
        $this->call(CandidateSkillsTableSeeder::class);
        $this->call(CandidatesTableSeeder::class);
        $this->call(CandidateWageExpectationsTableSeeder::class);
        $this->call(CustomerStcDetailsTableSeeder::class);
        $this->call(CandidateScreeningPersonalityTestQuestionsTableSeeder::class);
        $this->call(CandidateScreeningPersonalityTestQuestionOptionsTableSeeder::class);
        $this->call(MyersBriggsIndicatorTableSeeder::class);
        $this->call(MyersBriggsPersonalityTypeTableSeeder::class);
       
    }
}
