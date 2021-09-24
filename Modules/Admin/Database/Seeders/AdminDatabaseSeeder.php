<?php

namespace Modules\Admin\Database\Seeders;

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(JobRequisitionReasonLookupsTableSeeder::class);
        $this->call(AssignmentTypesLookupsTableSeeder::class);
        $this->call(CriteriaLookupsTableSeeder::class);
        $this->call(ExperienceLookupsTableSeeder::class);
        $this->call(FeedbackLookupsTableSeeder::class);
        $this->call(JobProcessLookupsTableSeeder::class);
        $this->call(LanguageLookupsTableSeeder::class);
        $this->call(PositionLookupsTableSeeder::class);
        $this->call(SkillLookupsTableSeeder::class);
        $this->call(TimingLookupsTableSeeder::class);
        $this->call(TrainingTimingLookupsTableSeeder::class);
        $this->call(CandidateAttachmentLookupsTableSeeder::class);
        $this->call(CandidateScreeningQuestionLookupsTableSeeder::class);
        $this->call(SecurityProviderLookupsTableSeeder::class);
         $this->call(TrackingProcessStepTableSeeder::class);
         $this->call(RfpAwardDatesLookupsTableSeeder::class);
        $this->call(DivisionLookupsTableDataSeeder::class);
        $this->call(StatusLogLookupsTableSeeder::class);
        $this->call(ScheduleAssignmentTypeLookupTableSeeder::class);
        $this->call(SecurityClearanceLookupsTableSeeder::class);
        $this->call(WorkTypesTableSeeder::class);
        $this->call(IndustrySectorLookupsTableSeeder::class);
        $this->call(RegionLookupsTableSeeder::class);
        $this->call(EmployeeRatingLookupsTableSeeder::class);
        $this->call(TrainingCategoriesTableSeeder::class);
        $this->call(LeaveReasonsTableSeeder::class);
        $this->call(ShiftTimingsTableSeeder::class);
        $this->call(TimeOffRequestTypeLookupTableSeeder::class);
        $this->call(CertificateMasterTableSeeder::class);
        $this->call(CandidateBrandAwarenessTableSeeder::class);
        $this->call(CapacityToolWorkClassificationAreaLookupsTableSeeder::class);
        $this->call(CapacityToolTaskFrequencyLookupsTableSeeder::class);
        $this->call(CapacityToolTaskTypeLookupsTableSeeder::class);
        $this->call(CapacityToolObjectiveLookupsTableSeeder::class);
        $this->call(CapacityToolSkillTypeLookupsTableSeeder::class);
        $this->call(CapacityToolStatusLookupsTableSeeder::class);
        $this->call(RoleLookupsTableSeeder::class);
        $this->call(YesOrNoTableSeeder::class);
        $this->call(CompetencyMatrixCategoryLookupTableSeeder::class);
        $this->call(CompetencyMatrixLookupTableSeeder::class);
        $this->call(CompetencyMatrixRatingLookupTableSeeder::class);
        $this->call(RateExperienceLookupsTableSeeder::class);
        $this->call(ClientFeedbackLookupsTableSeeder::class);
        $this->call(SeverityLookupsTableSeeder::class);
        $this->call(SiteNoteStatusLookupsTableSeeder::class);
        $this->call(ColorsTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(CommissionairesUnderstandingLookupsTableSeeder::class);
        $this->call(StcReportingTemplateRuleSeeder::class);
        $this->call(EmployeeWhisteblowerCategoryTableSeeder::class);
        $this->call(EmployeeWhistleblowerPrioritiesTableSeeder::class);
        $this->call(ExitResignationReasonLookupTableSeeder::class);
        $this->call(ExitTerminationReasonLookupTableSeeder::class);
        $this->call(EnglishRatingLookupsTableSeeder::class);
        $this->call(CandidateSecurityAwarenessTableSeeder::class);
        $this->call(MobileAppSettingsTableSeeder::class);
        // $this->call(DocumentNameDetailsTableSeeder::class);
        $this->call(VisitorLogTypeLookupsTableSeeder::class);
        $this->call(VisitorLogTemplateFieldsTableSeeder::class);
        $this->call(VisitorLogFeatureLookupsTableSeeder::class);
        $this->call(OtherCategoryLookupsTableSeeder::class);
        $this->call(IncidentPriorityLookupsTableSeeder::class);
        $this->call(OtherCategoryNamesTableSeeder::class);

        //dashboard
        $this->call(LandingPageWidgetLayoutsTableSeeder::class);
        $this->call(LandingPageModuleWidgetsTableSeeder::class);

    }
}
