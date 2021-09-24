<?php

namespace Modules\Reports\Repositories;

use App\Services\HelperService;
use App\Services\LocationService;
use Carbon\Carbon;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\Employee;
use Modules\Hranalytics\Models\CandidateEducation;
use Modules\Hranalytics\Models\CandidateEmployee;
use Modules\Hranalytics\Models\CandidateScreeningQuestion;
use Modules\Reports\Models\ReportTermination;

class TerminationReportRepository
{

    protected $reportTermination;

    public function __construct(
        ReportTermination $reportTerminationModel,
        LocationService $locationService
    ) {
        $this->reportTerminationModel = $reportTerminationModel;
        $this->locationService = $locationService;
    }

    /**
     * save to report termination
     * @param $request
     */
    public function save($request, $exitInterviewId)
    {
        // Get user id and project id
        $userId = (int)$request->get('employee_name_id');
        $customerId = $request->get('project_name_id');

        // Get candidate id from candidate employee
        $candidateEmployeeQuery = CandidateEmployee::where('user_id', $userId)->first();

        // Get user details from employee
        $employeeQuery = Employee::with('employeePosition')->where('user_id', $userId)->first();

        //Get exit interview id from employee exit interview
        $lastSavedExitInteviewId = $exitInterviewId;

        // Prepare Data for Termination Report
        $terminationReport = new ReportTermination;
        //userid
        $terminationReport->user_id = $userId;
        //candidateid
        if (!empty($candidateEmployeeQuery)) {
            $terminationReport->candidate_id = $candidateEmployeeQuery->candidate_id;
        }
        //exitInterview Id
        if (!empty($lastSavedExitInteviewId)) {
            $terminationReport->employee_exit_interview_id = $lastSavedExitInteviewId;
        }
        //age
        if (!empty($employeeQuery)) {
            $terminationReport->age = isset($employeeQuery->employee_dob)
                ? Carbon::parse($employeeQuery->employee_dob)->age
                : null;
        }
        // education
        if (!empty($candidateEmployeeQuery)) {
            $educationQuery = CandidateEducation::where('candidate_id', $candidateEmployeeQuery->candidate_id)
                ->select('grade', 'program')
                ->get()
                ->toArray();
            if (isset($educationQuery[0])) {
                $terminationReport->education_1 = $educationQuery[0]['grade'] . ' - ' . $educationQuery[0]['program'];
            }
            if (isset($educationQuery[1])) {
                $terminationReport->education_2 = $educationQuery[1]['grade'] . ' - ' . $educationQuery[1]['program'];
            }
            if (isset($educationQuery[2])) {
                $terminationReport->education_3 = $educationQuery[2]['grade'] . ' - ' . $educationQuery[2]['program'];
            }
        }
        //screening question avg count
        $letterCount = 0;
        if (!empty($candidateEmployeeQuery)) {
            $candidateScreeningQuestionQuery = CandidateScreeningQuestion::where(
                'candidate_id',
                $candidateEmployeeQuery->candidate_id
            )->select('answer')
                ->get()
                ->pluck('answer')
                ->toArray();
            foreach ($candidateScreeningQuestionQuery as $answer) {
                $letterCount += strlen($answer);
            }
            $terminationReport->screening_questions_avg_count = round($letterCount / 12);
        }
        //lenght of service
        if (!empty($employeeQuery)) {
            $terminationReport->length_of_service = isset($employeeQuery->employee_doj)
                ? HelperService::getFormattedDateDiff($employeeQuery->employee_doj)['formattedDiff']
                : null;
        }
        //no of guards at each site
        $rolesPermissionNames = ['client', 'admin', 'super_admin', 'area_manager'];
        $numberOfGuardsQuery = CustomerEmployeeAllocation::where('customer_id', $customerId)
            ->whereHas('user.roles', function ($queryRoles) use ($rolesPermissionNames) {
                return $queryRoles->whereNotIn('name', $rolesPermissionNames);
            })->get()->toArray();

        $numberOfGuards = collect($numberOfGuardsQuery)->count();
        $terminationReport->no_of_guards = $numberOfGuards;
        // position
        $position = $employeeQuery->position_id;
        if (isset($position)) {
            $terminationReport->position = $employeeQuery->employeePosition->position;
            //current wage
            $currentWage = CpidCustomerAllocations::with('cpid_lookup.cpidRates')
                ->where('customer_id', $customerId)
                ->whereHas('cpid_lookup', function ($queryPosition) use ($position) {
                    return $queryPosition->where('position_id', $position);
                })
                ->get()
                ->pluck('cpid_lookup.cpidRates.p_standard')
                ->toArray();

            if (!empty($currentWage)) {
                if (isset($currentWage[0])) {
                    $terminationReport->current_wage_1 = $currentWage[0];
                }
                if (isset($currentWage[1])) {
                    $terminationReport->current_wage_2 = $currentWage[1];
                }
                if (isset($currentWage[2])) {
                    $terminationReport->current_wage_3 = $currentWage[2];
                }
            }
        }

        //get customer postal code
        $customerQuery = Customer::where('id', $customerId)->first();

        $customerPostalCode = ($customerQuery->postal_code) != "" ? $customerQuery->postal_code : null;

        //get employee postal code
        $employeePostalCode = ($employeeQuery->employee_postal_code) != "" ? $employeeQuery->employee_postal_code : null;

        if (isset($employeePostalCode, $customerPostalCode)) {
            $googleMap = $this->getDistanceAndTime(
                $employeePostalCode,
                $customerPostalCode
            );

            //distance between work and home
            $terminationReport->distance_between_work_and_home = isset($googleMap['distance'])
                ? $googleMap['distance'] : null;
            //time between work and home
            $terminationReport->time_between_work_and_home = isset($googleMap['time'])
                ? $googleMap['time'] : null;
        }

        //dd($terminationReport);
        return $terminationReport->save();
    }

    /**
     * distance and time between home and work for guard
     * @param $employeePostalCode, $customerPostalCode
     * @return array
     */
    public function getDistanceAndTime($employeePostalCode, $customerPostalCode)
    {
        $distanceTime = array();
        //google api to find distance between two points using postal code
        $google_api_key = config('globals.google_api_curl_key');
        try {
            $employeePostalCode = $this->locationService->urlEncodeCnPostalCode($employeePostalCode);
            $customerPostalCode = $this->locationService->urlEncodeCnPostalCode($customerPostalCode);
            \App\Services\HelperService::googleAPILog('distancematrix','Modules\Reports\Repositories\TerminationReportRepository\getDistanceAndTime');
            $json = file_get_contents("https://maps.google.com/maps/api/distancematrix/json?units=metric&origins="
                . $employeePostalCode . "&destinations=" . $customerPostalCode . "&key=" . $google_api_key);
            $google = json_decode($json);
        } catch (\Exception $e) {
            error_log($e);
            return null;
        }

        if(empty($google->rows)) {
            return null;
        }

        if($google->rows[0]->elements[0]->status == "NOT_FOUND") {
            return null;
        }
        $distanceTime['distance'] = $google->rows[0]->elements[0]->distance->text;
        $distanceTime['time'] = $google->rows[0]->elements[0]->duration->text;

        return $distanceTime;
    }

    /**
     * get termination data from table
     * @param $startDate $endDate
     * @return $json
     */
    public function getTerminationReportData($request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $reportTerminationQuery = ReportTermination::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->with('user.employee')
            ->with(['employeeExitInterview' => function ($qResig) {
                return $qResig->where('exit_interview_reason_id', 1)->with('reason_detail_resignation')
                    ->orWhere('exit_interview_reason_id', 2)->with('reason_detail_termination');
            }])
            ->with(['candidate' => function ($qCandidate) {
                return $qCandidate
                    ->with(['latestJobApplied' => function ($qCandidateJobs) {
                        return $qCandidateJobs->select(
                            'candidate_id',
                            'brand_awareness_id',
                            'security_awareness_id',
                            'average_score',
                            'english_rating_id'
                        )
                            ->with(['candidate_brand_awareness' => function ($qBrandAwarness) {
                                return $qBrandAwarness->select('id', 'answer');
                            }])
                            ->with(['candidate_security_awareness' => function ($qSecurity) {
                                return $qSecurity->select('id', 'answer');
                            }])
                            ->with(['englishProficiency' => function ($qEnglishProf) {
                                return $qEnglishProf->select('id', 'english_ratings');
                            }]);
                    }])
                    ->with(['comissionaires_understanding.candidateUnderstandingLookup' => function ($qCgl) {
                        return $qCgl->select('id', 'commissionaires_understandings');
                    }])
                    ->with(['wageExpectation' => function ($qWageExpect) {
                        return $qWageExpect->with('wageprovider')
                            ->select(
                                'candidate_id',
                                'wage_last_hourly',
                                'wage_last_provider',
                                'wage_last_provider_other'
                            );
                    }])
                    ->with(['securityclearance' => function ($qSecurityClearance) {
                        return $qSecurityClearance->select('candidate_id', 'work_status_in_canada');
                    }])
                    ->with(['securityproximity' => function ($qLicense) {
                        return $qLicense->select(
                            'candidate_id',
                            'driver_license',
                            'access_vehicle',
                            'access_public_transport'
                        );
                    }])
                    ->with(['languages' => function ($qEnglishLanguage) {
                        return $qEnglishLanguage->whereIn('language_id', [1, 2])->select(
                            'candidate_id',
                            'language_id',
                            'speaking'
                        );
                    }])
                    ->with('skills')
                    ->with(['technicalSummaryTrashed' => function ($qSmartphonetype) {
                        return $qSmartphonetype->select('id', 'type');
                    }])
                    ->with(['miscellaneous' => function ($qMiscell) {
                        return $qMiscell->select(
                            'candidate_id',
                            'veteran_of_armedforce',
                            'dismissed',
                            'criminal_convicted',
                            'career_interest'
                        );
                    }])
                    ->with(['personality_scores' => function ($qPersonalityScore) {
                        return $qPersonalityScore->select('candidate_id', 'score');
                    }]);
            }])
            ->get()
            ->toArray();

        $report = array();
        //dd($reportTerminationQuery);

        foreach ($reportTerminationQuery as $key => $value) {

            //Data from User and Relations starts here

            //employee number
            $report[$key]['employee_no'] = $value['user']['employee']['employee_no'];
            //employee name
            $report[$key]['employee_name'] = $value['user']['full_name'];
            //date of conversion
            $report[$key]['date_of_conversion'] = $value['user']['employee']['employee_doj'];
            //city
            $report[$key]['city'] = $value['user']['employee']['employee_city'];
            //postal code
            $report[$key]['postal_code'] = $value['user']['employee']['employee_postal_code'];
            //years of experience
            $report[$key]['years_of_experience'] = $value['user']['employee']['years_of_security'];
            //employee rating
            $report[$key]['employee_rating'] = isset($value['user']['employee']['employee_rating'])
                ? number_format((float) $value['user']['employee']['employee_rating'], 2, '.', '')
                : null;

            //Data from User and Relations ends here

            //Data from Candidate and Relations starts here
            //familiarity
            $report[$key]['familiarity'] =
                (isset($value['candidate']['latest_job_applied']['candidate_brand_awareness']['answer']))
                    ? $value['candidate']['latest_job_applied']['candidate_brand_awareness']['answer']
                    : null;
            //understanding of cgl
            $report[$key]['understanding_of_cgl'] =
                isset($value['candidate']
                    ['comissionaires_understanding'][0]
                    ['candidate_understanding_lookup']
                    ['commissionaires_understandings'])
                    ? $value['candidate']
                ['comissionaires_understanding'][0]
                ['candidate_understanding_lookup']
                ['commissionaires_understandings']
                    : null;
            //other companies
            $report[$key]['other_companies'] =
                isset($value['candidate']['latest_job_applied']['candidate_security_awareness']['answer'])
                    ? $value['candidate']['latest_job_applied']['candidate_security_awareness']['answer']
                    : null;
            //last wage
            $report[$key]['last_wage'] = isset($value['candidate']['wage_expectation']['wage_last_hourly'])
                ? $value['candidate']['wage_expectation']['wage_last_hourly']
                : null;
            //last provider
            $report[$key]['last_provider'] = isset($value['candidate']['wage_expectation']['wage_last_provider'])
                ? $value['candidate']['wage_expectation']['wageprovider']['security_provider']
                : null;
            //last provider other
            $report[$key]['last_provider_other'] = isset($value['candidate']['wage_expectation']['wage_last_provider'])
                ? $value['candidate']['wage_expectation']['wage_last_provider_other']
                : null;
            //canadian status
            $report[$key]['canadian_status'] = isset($value['candidate']['securityclearance']['work_status_in_canada'])
                ? $value['candidate']['securityclearance']['work_status_in_canada']
                : null;
            //license
            $report[$key]['license'] = isset($value['candidate']['securityproximity']['driver_license'])
                ? $value['candidate']['securityproximity']['driver_license']
                : null;
            //access to vehicle
            $report[$key]['access_to_vehicle'] = isset($value['candidate']['securityproximity']['access_vehicle'])
                ? $value['candidate']['securityproximity']['access_vehicle']
                : null;
            //public transit
            $report[$key]['public_transit'] = isset($value['candidate']['securityproximity']['access_public_transport'])
                ? $value['candidate']['securityproximity']['access_public_transport']
                : null;
            //language
            $language = collect($value['candidate']['languages']);
            $englishSpeaking = $language->where('language_id', 1)->pluck('speaking');
            $frenchSpeaking = $language->where('language_id', 2)->pluck('speaking');

            $report[$key]['english_speaking'] = isset($englishSpeaking[0]) ? $englishSpeaking[0] : null;
            $report[$key]['french_speaking'] = isset($frenchSpeaking[0]) ? $frenchSpeaking[0] : null;

            //skills
            $skills = collect($value['candidate']['skills']);
            $msword = $skills->where('skill_id', 1)->pluck('skill_level');
            $msexcel = $skills->where('skill_id', 2)->pluck('skill_level');
            $mspowerpoint = $skills->where('skill_id', 3)->pluck('skill_level');
            $customerService = $skills->where('skill_id', 4)->pluck('skill_level');
            $leadership = $skills->where('skill_id', 5)->pluck('skill_level');
            $problemsolving = $skills->where('skill_id', 6)->pluck('skill_level');
            $timemgmt = $skills->where('skill_id', 7)->pluck('skill_level');

            $report[$key]['msword'] = isset($msword[0]) ? $msword[0] : null;
            $report[$key]['msexcel'] = isset($msexcel[0]) ? $msexcel[0] : null;
            $report[$key]['mspowerpoint'] = isset($mspowerpoint[0]) ? $mspowerpoint[0] : null;
            $report[$key]['customerService'] = isset($customerService[0]) ? $customerService[0] : null;
            $report[$key]['leadership'] = isset($leadership[0]) ? $leadership[0] : null;
            $report[$key]['problemsolving'] = isset($problemsolving[0]) ? $problemsolving[0] : null;
            $report[$key]['timemgmt'] = isset($timemgmt[0]) ? $timemgmt[0] : null;

            //smartphone
            $report[$key]['smartphone'] = isset($value['candidate']) ?
                (isset($value['candidate']['technical_summary_trashed']['type']) ? 'Yes' : 'No')
                : null;
            $report[$key]['type_of_smartphone'] = isset($value['candidate']['technical_summary_trashed']['type'])
                ? $value['candidate']['technical_summary_trashed']['type']
                : null;
            $report[$key]['proficiency_with_phone'] = isset($value['candidate']['smart_phone_skill_level'])
                ? $value['candidate']['smart_phone_skill_level']
                : null;

            //miscellaneous
            $report[$key]['military_experience'] = isset($value['candidate']['miscellaneous']['veteran_of_armedforce'])
                ? $value['candidate']['miscellaneous']['veteran_of_armedforce']
                : null;
            $report[$key]['dismissals'] = isset($value['candidate']['miscellaneous']['dismissed'])
                ? $value['candidate']['miscellaneous']['dismissed']
                : null;
            $report[$key]['criminal_convictions'] = isset($value['candidate']['miscellaneous']['criminal_convicted'])
                ? $value['candidate']['miscellaneous']['criminal_convicted']
                : null;
            $report[$key]['career_interest'] = isset($value['candidate']['miscellaneous']['career_interest'])
                ? $value['candidate']['miscellaneous']['career_interest']
                : null;

            $report[$key]['screening_questions_score'] = isset($value['candidate']['latest_job_applied']['average_score'])
                ? $value['candidate']['latest_job_applied']['average_score']
                : null;

            //english fluency
            $report[$key]['english_fluency'] =
                isset($value['candidate']['latest_job_applied']['english_proficiency']['english_ratings'])
                    ? $value['candidate']['latest_job_applied']['english_proficiency']['english_ratings']
                    : null;
            // personality
            $report[$key]['personality'] =
                isset($value['candidate']['personality_scores'][0]['score'])
                    ? $value['candidate']['personality_scores'][0]['score']
                    : null;
            //Data from Candidate and Relations ends here

            //Data from Employee Exit Interview starts here

            //reason
            $report[$key]['reason_type'] = $value['employee_exit_interview']['exit_interview_reason_id'] == 1
                ? 'Resignation' : 'Termination';
            //reason detail resignation
            $report[$key]['reason_category'] = $value['employee_exit_interview']['exit_interview_reason_id'] == 1
                ? $value['employee_exit_interview']['reason_detail_resignation']['reason']
                : $value['employee_exit_interview']['reason_detail_termination']['reason'];

            $report[$key]['unique_id'] = $value['employee_exit_interview']['unique_id'];
            // $report[$key]['exit_interview_date'] =
            // Carbon::parse($value['employee_exit_interview']['created_at'])->format('Y-m-d');

            $report[$key]['exit_interview_date'] = $value['employee_exit_interview']['created_at'];

            //Data from Employee Exit Interview ends here

            //Data from Termination Report starts here
            $report[$key]['age'] = $value['age'];
            $report[$key]['education_1'] = $value['education_1'];
            $report[$key]['education_2'] = $value['education_2'];
            $report[$key]['education_3'] = $value['education_3'];
            $report[$key]['screening_questions_avg_count'] = $value['screening_questions_avg_count'];
            $report[$key]['length_of_service'] = $value['length_of_service'];
            $report[$key]['no_of_guards'] = $value['no_of_guards'];
            $report[$key]['position'] = $value['position'];
            $report[$key]['current_wage_1'] = $value['current_wage_1'];
            $report[$key]['current_wage_2'] = $value['current_wage_2'];
            $report[$key]['current_wage_3'] = $value['current_wage_3'];
            $report[$key]['distance_between_work_and_home'] = $value['distance_between_work_and_home'];
            $report[$key]['time_between_work_and_home'] = $value['time_between_work_and_home'];
            //Data from Termination Report ends here
        }
        //dd($report);
        return $report;
    }
}
