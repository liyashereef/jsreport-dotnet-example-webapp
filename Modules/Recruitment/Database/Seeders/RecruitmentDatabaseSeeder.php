<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecruitmentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->call(RecruitmentModuleTableSeeder::class);
        $this->call(RolesAndPermissionsRecruitmentMastersTableSeeder::class);
        $this->call(ModulePermissionsRecruitmentMastersTableSeeder::class);
        $this->call(RolesAndPermissionsRecruitmentJobSeeder::class);
        $this->call(ModulePermissionsRecruitmentJobSeeder::class);
        $this->call(RolesAndPermissionsRecruitmentCandidateSeeder::class);
        $this->call(ModulePermissionsRecruitmentCandidateSeeder::class);
        $this->call(RecBrandAwarenessTableSeeder::class);
        $this->call(RecSecurityAwarenessTableSeeder::class);
        $this->call(RecCommissionairesUnderstandingLookupsTableSeeder::class);
        $this->call(RecExperienceLookupsTableSeeder::class);
        $this->call(RecCriteriaLookupsTableSeeder::class);
        $this->call(RecUniformMeasuremntPointsTableSeeder::class);
        $this->call(RecCandidateAttachmentLookupsTableSeeder::class);
        $this->call(RecJobProcessLookupsTableSeeder::class);
        $this->call(RecAssignmentTypesLookupTableSeeder::class);
//        $this->call(RecCandidateAvailabilitiesTableSeeder::class);
//        $this->call(RecCandidateAwarenessTableSeeder::class);
//        $this->call(RecCandidateMiscellaneousesTableSeeder::class);
        $this->call(RecCandidateScreeningPersonalityTestQuestionTableSeeder::class);
        $this->call(RecCandidateScreeningPersonalityTestQuestionOptionsTableSeeder::class);
//        $this->call(RecCandidateSecurityGuardingExperincesTableSeeder::class);
//        $this->call(RecCandidateWageExpectationsTableSeeder::class);
        $this->call(RecCompetencyMatrixCategoryLookupTableSeederTableSeeder::class);
        $this->call(RecCompetencyMatrixLookupTableSeederTableSeeder::class);
        $this->call(RecCompetencyMatrixRatingLookupTableSeederTableSeeder::class);
        $this->call(RecEnglishRatingLookupsTableSeederTableSeeder::class);
        $this->call(RecJobRequisitionReasonLookupTableSeeder::class);
        $this->call(RecMyersBriggsPersonalityTypeTableSeeder::class);
        $this->call(RecProcessStepsTableSeeder::class);
        $this->call(RecRateExperienceLookupsTableSeederTableSeeder::class);
        $this->call(RecSecurityClearanceLookupsTableSeederTableSeeder::class);
        $this->call(RecTimingLookupTableSeeder::class);
        $this->call(RecTrainingTimingLookupTableSeeder::class);
        $this->call(RecJobTicketSettingTableSeeder::class);
        $this->call(RecScoreCriteriaTableSeeder::class);
        $this->call(RecUseOfForceLookupTableSeeder::class);
        $this->call(RecFeedbackLookupTableSeederTableSeeder::class);
//        $this->call(RecCandidateSecurityClearancesTableSeeder::class);
        $this->call(RecCriteriaLookupsTableSeeder::class);
        $this->call(RecMatchScoreCriteriaMappingsTableSeeder::class);
        $this->call(RecMyersBriggsIndicatorTableSeeder::class);
        $this->call(RecProcessTabsTableSeeder::class);
    }
}
