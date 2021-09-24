<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use App\Services\LocationService;
use Auth;
use Illuminate\Support\Arr;
use Modules\Admin\Models\TrackingProcessLookup;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Models\CandidateAttachment;
use Modules\Hranalytics\Models\CandidateEmployee;
use Modules\Hranalytics\Models\CandidateForceCertification;
use Modules\Hranalytics\Models\CandidateJob;
use Modules\Hranalytics\Models\CandidateJobInterview;
use Modules\Hranalytics\Models\CandidateSecurityGuardingExperince;
use Modules\Hranalytics\Models\CandidateTracking;
use Modules\Hranalytics\Models\EventLogEntry;

class CandidateRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $candidateJobModel, $candidateModel, $trackingProcessLookupModel, $candidateTracking, $candidateJobInterview;
    /**
     * @var HelperService
     */
    private $helperService;
    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * Create a new CandidateRepository instance.
     *
     * @param CandidateJob $candidateJobModel
     * @param Candidate $candidateModel
     * @param TrackingProcessLookup $trackingProcessLookupModel
     * @param CandidateTracking $candidateTracking
     * @param CandidateJobInterview $candidateJobInterview
     * @param HelperService $helperService
     * @param LocationService $locationService
     */
    public function __construct(
        CandidateJob $candidateJobModel,
        Candidate $candidateModel,
        TrackingProcessLookup $trackingProcessLookupModel,
        CandidateTracking $candidateTracking,
        CandidateJobInterview $candidateJobInterview,
        HelperService $helperService,
        LocationService $locationService
    ) {
        $this->directory_seperator = "/";
        $this->candidateModel = $candidateModel;
        $this->candidateJobModel = $candidateJobModel;
        $this->trackingProcessLookupModel = $trackingProcessLookupModel;
        $this->candidateTracking = $candidateTracking;
        $this->candidateJobInterview = $candidateJobInterview;
        $this->helperService = $helperService;
        $this->locationService = $locationService;
    }

    /**
     * To get all candidate those who are applied for a job - preparing the data
     *
     * @param [type] $candidate_selection_status
     * @param [type] $request
     * @param string $order_by
     * @return void
     */
    public function prepareCandidatesRecords(
        $candidate_selection_status = null,
        $request = null,
        $type_of_records_request = null,
        $order_by = 'name',
        $customer_session = false,
        $widgetRequest = false,
        $candidateIdArr = null
    ) {

        /** $customer_session for dashboard filter, get customer ids from session and find */
        $user = \Auth::user();

        //from age
        $filterCandidateFromYear = null;
        if (($request != null) && $request->has('age_from') && !empty($request->get('age_from'))) {
            $filterCandidateFromYear = date("Y-12-31 H:i:s", strtotime("-" . $request->get('age_from') . " years 23:59:59"));
        }

        //to Age
        $filterCandidateToYear = null;
        if (($request != null) && $request->has('age_to') && !empty($request->get('age_to'))) {
            $filterCandidateToYear = date("Y-01-01 H:i:s", strtotime("-" . $request->get('age_to') . " years 00:00:00"));
        }

        $query = $this->candidateModel
        //age based filtering
            ->when(($filterCandidateFromYear != null && $filterCandidateToYear != null), function ($query) use ($filterCandidateToYear, $filterCandidateFromYear) {
                $query->whereBetween('dob', [$filterCandidateToYear, $filterCandidateFromYear]);
            })
        //application date based filtering
            ->when($request != null, function ($query) use ($request) {
                if (null !== ($request->get('application_date'))) {
                    $query->whereHas('jobsApplied', function ($query) use ($request) {
                        $query->when((($request != null) && $request->has('application_date') && !empty($request->get('application_date'))), function ($query) use ($request) {
                            $applicationStartDate = date("Y-m-d H:i:s", strtotime($request->get('application_date') . " 00:00:00"));
                            $applicationEndDate = date("Y-m-d H:i:s", strtotime($request->get('application_date') . " 23:59:59"));
                            if ($request->get('application_date_condition') == "=") {
                                $query->whereBetween('submitted_date', [$applicationStartDate, $applicationEndDate]);
                            } elseif ($request->get('application_date_condition') == "<=") {
                                $query->where('submitted_date', "<=", $applicationEndDate);
                            } else {
                                $query->where('submitted_date', ">=", $applicationStartDate);
                            }
                        });
                    });
                }
                //candidate stage based filtering
                if (null !== ($request->get('candidate_stage'))) {
                    $candidateStage = array_filter($request->get('candidate_stage'));
                    if (!empty($candidateStage)) {
                        $query->whereHas('lastTrack', function ($query) use ($candidateStage) {
                            $query->when((!empty($candidateStage)), function ($query) use ($candidateStage) {
                                $query->whereIn('lookup_id', $candidateStage);
                            });
                        });
                    }
                }

                //candidate personality score
                if (null !== ($request->get('personality_type'))) {
                    $query->whereHas('personality_scores.score_type', function ($query) use ($request) {
                        $query->when((($request != null) && $request->has('personality_type') && !empty($request->get('personality_type'))), function ($query) use ($request) {
                            $query->where('id', $request->get('personality_type'));
                        });
                    });
                }

                //miscellaneous
                if (null !== ($request->get('career_interest'))) {
                    $query->whereHas('miscellaneous', function ($query) use ($request) {
                        $query->when((($request != null) && $request->has('career_interest') && !empty($request->get('career_interest'))), function ($query) use ($request) {
                            $query->where('career_interest', $request->get('career_interest'));
                        });
                    });
                }
            })
            ->whereHas('latestJobApplied', function ($query) use ($candidate_selection_status, $user, $request) {
                $query->where('status', '=', 'Applied')
                    ->when($candidate_selection_status != null, function ($query) use ($candidate_selection_status) {
                        $query->where('candidate_status', '=', $candidate_selection_status);
                    });
                if (null != $request && null !== ($request->get('candidate_score'))) {
                    $query->where('average_score', $request->get('candidate_score_condition'), $request->get('candidate_score'));
                }
            })
            ->when(
                $type_of_records_request == null,
                function ($query) use ($user, $request, $customer_session) {
                    $query->whereHas('latestJobApplied.job', function ($query) use ($user, $request, $customer_session) {

                        $query->when(!$user->hasAnyPermission(['view_all_candidates', 'admin', 'super_admin']), function ($query) use ($user) {
                            //$query->where('user_id', '=', $user->id);
                            //$query->when($user->can('hr-tracking'), function ($query) use ($user) {
                            $query->where('hr_rep_id', '=', $user->id)
                                ->orWhere('user_id', '=', $user->id);
                            //});
                        });

                        if (null != $request && null !== ($request->get('job_applied'))) {
                            $query->where('customer_id', '=', $request->get('job_applied'));
                        }

                        /** START ** Get Customer Ids from Session and Filter */
                        if ($customer_session) {
                            $customer_ids = $this->helperService->getCustomerIds();
                            if (!empty($customer_ids)) {
                                $query->whereIn('customer_id', $customer_ids);
                            }
                        }
                        /** END ** Get Customer Ids from Session and Filter */
                    });
                },
                /** START ** when $type_of_records_request not is_null..used for candidate tracking page if track all customer permission is given /**/
                function ($query) use ($user, $request, $customer_session) {

                    $query->whereHas('latestJobApplied.job', function ($query) use ($user, $request, $customer_session) {

                        $query->when(!$user->hasAnyPermission(['track_all_candidates', 'view_all_candidates_candidate_onboardingstatus', 'admin', 'super_admin']), function ($query) use ($user) {
                            //$query->where('user_id', '=', $user->id);
                            //$query->when($user->can('hr-tracking'), function ($query) use ($user) {
                            $query->where('hr_rep_id', '=', $user->id)
                                ->orWhere('user_id', '=', $user->id);
                            //});
                        });

                        if (null != $request && null !== ($request->get('job_applied'))) {
                            $query->where('customer_id', '=', $request->get('job_applied'));
                        }

                        /** START ** Get Customer Ids from Session and Filter */
                        if ($customer_session) {
                            $customer_ids = $this->helperService->getCustomerIds();
                            if (!empty($customer_ids)) {
                                $query->whereIn('customer_id', $customer_ids);
                            }
                        }
                        /** END ** Get Customer Ids from Session and Filter */
                    });
                }
            )
        /** End **  when $type_of_records_request not is_null..used for candidate tracking page if track all customer permission is given */
            ->when($request != null, function ($query) use ($request) {

                if (null !== ($request->get('location'))) {
                    $query->where('city', 'like', "%" . $request->get('location') . "%");
                }
                if (null !== ($request->get('availability'))) {
                    $query->whereHas('availability', function ($query) use ($request) {
                        $query->where('current_availability', '=', $request->get('availability'));
                    });
                }
                if (null !== ($request->get('wage_low')) || null !== ($request->get('wage_high')) || null !== ($request->get('current_wage'))) {
                    $query->whereHas('wageExpectation', function ($query) use ($request) {
                        if (null !== ($request->get('wage_low'))) {
                            $query->where('wage_expectations_from', $request->get('wage_low_condition'), $request->get('wage_low'));
                        }
                        if (null !== ($request->get('wage_high'))) {
                            $query->where('wage_expectations_to', $request->get('wage_high_condition'), $request->get('wage_high'));
                        }
                        if (null !== ($request->get('current_wage'))) {
                            $query->where('wage_last_hourly', $request->get('current_wage_condition'), $request->get('current_wage'));
                        }
                    });
                }

                if (
                    (null !== ($request->get('years_experience')))
                    || (!empty($request->get('license_expiry_from')))
                    || (null !== ($request->get('guard_license_expiry')))
                    || (null !== ($request->get('cpr_expiry')))
                    || (null !== ($request->get('first_aid_expiry')))
                ) {
                    $query->whereHas('guardingExperience', function ($query) use ($request) {
                        if (null !== ($request->get('years_experience'))) {
                            $query->where('years_security_experience', $request->get('years_experience_condition'), $request->get('years_experience'));
                        }
                        if (!empty($request->get('license_expiry_from'))) {
                            $query->where(function ($query) use ($request) {
                                return $query->whereBetween('expiry_guard_license', [$request->get('license_expiry_from'), $request->get('license_expiry_to')])
                                    ->orWhereBetween('expiry_first_aid', [$request->get('license_expiry_from'), $request->get('license_expiry_to')])
                                    ->orWhereBetween('expiry_cpr', [$request->get('license_expiry_from'), $request->get('license_expiry_to')]);
                            });
                        }

                        //Guard license expiry filter
                        if (null !== ($request->get('guard_license_expiry'))) {
                            $guardExpiryStartDate = date("Y-m-d H:i:s", strtotime($request->get('guard_license_expiry') . " 00:00:00"));
                            $guardExpiryAidEndDate = date("Y-m-d H:i:s", strtotime($request->get('guard_license_expiry') . " 23:59:59"));
                            if ($request->get('guard_license_expiry_condition') == "=") {
                                $query->whereBetween('expiry_guard_license', [$guardExpiryStartDate, $guardExpiryAidEndDate]);
                            } elseif ($request->get('guard_license_expiry_condition') == "<=") {
                                $query->where('expiry_guard_license', "<=", $guardExpiryAidEndDate);
                            } else {
                                $query->where('expiry_guard_license', ">=", $guardExpiryStartDate);
                            }
                        }

                        // CPR expiry filter
                        if (null !== ($request->get('cpr_expiry'))) {
                            $cprExpiryStartDate = date("Y-m-d H:i:s", strtotime($request->get('cpr_expiry') . " 00:00:00"));
                            $cprExpiryAidEndDate = date("Y-m-d H:i:s", strtotime($request->get('cpr_expiry') . " 23:59:59"));
                            if ($request->get('cpr_expiry_condition') == "=") {
                                $query->whereBetween('expiry_cpr', [$cprExpiryStartDate, $cprExpiryAidEndDate]);
                            } elseif ($request->get('cpr_expiry_condition') == "<=") {
                                $query->where('expiry_cpr', "<=", $cprExpiryAidEndDate);
                            } else {
                                $query->where('expiry_cpr', ">=", $cprExpiryStartDate);
                            }
                        }

                        // first aid expiry filter
                        if (null !== ($request->get('first_aid_expiry'))) {
                            $firstAidStartDate = date("Y-m-d H:i:s", strtotime($request->get('first_aid_expiry') . " 00:00:00"));
                            $firstAidEndDate = date("Y-m-d H:i:s", strtotime($request->get('first_aid_expiry') . " 23:59:59"));
                            if ($request->get('first_aid_expiry_condition') == "=") {
                                $query->whereBetween('expiry_first_aid', [$firstAidStartDate, $firstAidEndDate]);
                            } elseif ($request->get('first_aid_expiry_condition') == "<=") {
                                $query->where('expiry_first_aid', "<=", $firstAidEndDate);
                            } else {
                                $query->where('expiry_first_aid', ">=", $firstAidStartDate);
                            }
                        }
                    });
                }

                if (
                    (null !== ($request->get('years_canada')))
                    || (null !== ($request->get('work_status')))
                ) {
                    $query->whereHas('securityclearance', function ($query) use ($request) {
                        // years in canada filter
                        if (null !== ($request->get('years_canada'))) {
                            $query->whereRaw('CAST(years_lived_in_canada as UNSIGNED) ' . $request->get('years_canada_condition') . '?', $request->get('years_canada'));
                        }

                        // work status
                        if (null !== ($request->get('work_status'))) {
                            $query->where('work_status_in_canada', $request->get('work_status'));
                        }
                    });
                }

                if (
                    (null !== ($request->get('drivers_license')))
                ) {
                    // drivers license filter
                    $query->whereHas('securityproximity', function ($query) use ($request) {
                        if (null !== ($request->get('drivers_license'))) {
                            $query->where('driver_license', $request->get('drivers_license'));
                        }
                    });
                }

                if (
                    (null !== ($request->get('use_of_force')))
                ) {
                    //use of force filter
                    $query->whereHas('force', function ($query) use ($request) {
                        if (null !== ($request->get('use_of_force'))) {
                            $query->where('force', $request->get('use_of_force'));
                        }
                    });
                }

                if (
                    (null !== ($request->get('vet')))
                    || (null !== ($request->get('indigienous')))
                ) {
                    // veteran and indigienous filter
                    $query->whereHas('miscellaneous', function ($query) use ($request) {
                        // veteran filter
                        if (null !== ($request->get('vet'))) {
                            $query->where('veteran_of_armedforce', $request->get('vet'));
                        }

                        // Indigienous filter
                        if (null !== ($request->get('indigienous'))) {
                            $query->where('is_indian_native', $request->get('indigienous'));
                        }
                    });
                }

                //referal availability
                if (
                    (null !== ($request->get('orientation')))
                    || (null !== ($request->get('position_availibility')))
                    || (null !== ($request->get('starting_time')))
                    || (null !== ($request->get('floater_hours')))
                ) {

                    $query->whereHas('referalAvailibility', function ($q) use ($request) {
                        // orientation
                        if (null !== ($request->get('orientation'))) {
                            $q->where('orientation', $request->get('orientation'));
                        }

                        // willing to work as spare
                        if (null !== ($request->get('position_availibility'))) {
                            $q->where('position_availibility', $request->get('position_availibility'));
                        }

                        // how soon can you start?
                        if (null !== ($request->get('starting_time'))) {
                            $q->where('starting_time', $request->get('starting_time'));
                        }

                        // hours per week willing to work
                        if (null !== ($request->get('floater_hours'))) {
                            $q->where('floater_hours', $request->get('floater_hours_condition'), $request->get('floater_hours'));
                        }

                    });
                }
            })
        // english filter
            ->when($request != null && null !== $request->get('english'), function ($query) use ($request) {
                $query->whereHas('languages', function ($q) use ($request) {
                    $q->where('language_id', 1)
                        ->where('speaking', $request->get('english'));
                });
            })
        // french filter
            ->when($request != null && null !== $request->get('french'), function ($query) use ($request) {
                $query->whereHas('languages', function ($q) use ($request) {
                    $q->where('language_id', 2)
                        ->where('speaking', $request->get('french'));
                });
            })
            ->with([
                'latestJobApplied.job.customer',
                // 'latestJobApplied.job.positionBeeingHired',
                // 'latestJobApplied.job.reason',
                // 'latestJobApplied.job.assignmentType',
                'latestJobApplied.feedback',
                // 'latestJobApplied.reassigned_job',
                // 'latestJobApplied.reassigned_job.customer',
                'latestJobApplied.job.assignee',
                // 'latestJobApplied.candidate_brand_awareness',
                // 'latestJobApplied.candidate_security_awareness',
                // 'comissionaires_understanding.candidateUnderstandingLookup',
                'guardingExperience',
                'force.force_lookup',
                'wageExpectation',
                'securityclearance',
                'other_languages',
                // 'wageExpectation.rating',
                // 'wageExpectation.lastrole',
                // 'wageExpectation.wageprovider',
                // 'availability',
                'referalAvailibility',
                'securityproximity',
                'employment_history_latest',
                // 'references',
                // 'educations',
                'languages',
                // 'skills.skill_lookup',
                // 'technicalSummary',
                'experience',
                // 'experience.division',
                'miscellaneous',
                'lastTrack',
                // 'trackings',
                // 'trackings.tracking_process',
                // 'trackings.entered_by',
                // 'lastTrack.tracking_process',
                // 'lastTrack.entered_by',
                // 'termination',
            ])
            ->when($type_of_records_request != null, function ($query) use ($type_of_records_request) {
                switch ($type_of_records_request) {
                    case 'tracking_summary':
                        $query->wherehas('lastTrack');
                        break;
                }
            });

        $query->when($order_by != null, function ($query) use ($order_by) {
            $query->orderBy($order_by);
        });
        if (!empty($candidateIdArr)) {
            $query->whereIn('id', $candidateIdArr);
        }
        if ($widgetRequest) {
            $count = (int) config('dashboard.candidate_screening_summary_row_limit');
            $query->limit($count);
        }
        return $query;
    }

    /**
     * To get all candidate tracking users list - for checking whether to show icon button
     *
     * @param [type] $candidate_selection_status
     * @param [type] $request
     * @param string $order_by
     * @return void
     */
    public function hrTrackingList($user, $candidate_selection_status)
    {
        $query = $this->candidateModel
            ->wherehas('latestJobApplied', function ($query) use ($candidate_selection_status, $user) {
                $query->where('status', '=', 'Applied')
                    ->when($candidate_selection_status != null, function ($query) use ($candidate_selection_status) {
                        $query->where('candidate_status', '=', $candidate_selection_status);
                    });
            })
            ->wherehas('latestJobApplied.job', function ($query) use ($user) {
                $trackAllPermission = $user->can('track_all_candidates') || $user->can('admin') || $user->can('super_admin');
                $query->when(!$trackAllPermission, function ($query) use ($user) {
                    $query->where('hr_rep_id', '=', $user->id)
                        ->orWhere('user_id', '=', $user->id);
                });
            })->pluck('id');
        return $query;
    }

    /**
     * To get a candidate
     *
     * @param [type] $candidate_id
     * @return void
     */
    public function getCandidate($candidate_id)
    {
        return $this->candidateModel->where("id", $candidate_id)->first();
    }

    /**
     * To get candidate records
     *
     * @param [type] $candidate_selection_status
     * @return void
     */
    // public function getCandidates($candidate_selection_status = null, $request = null)
    public function getCandidates($candidate_selection_status = null, $request = null, $type_of_records_request = null, $order_by = 'name', $customer_session = false, $widgetRequest = false, $candidateIdArr = null)
    {
        $records = $this->prepareCandidatesRecords($candidate_selection_status, $request, $type_of_records_request, $order_by, $customer_session, $widgetRequest, $candidateIdArr)->get();
        return $records;
    }

    public function getCandidatesById($candidate_id_arr)
    {
        return $this->getCandidates(null, null, null, 'name', null, null, $candidate_id_arr);
    }

    /**
     * To get a summary of HR tracking
     *
     * @return void
     */
    public function getTrackingSummary()
    {
        $records = $this->prepareCandidatesRecords('Proceed', null, 'tracking_summary')->get();
        return $records;
    }

    /**
     * To get candidates as dropdown list
     *
     * @param [type] $candidate_status
     * @return void
     */
    public function getCandidateAsList($candidate_selection_status = null)
    {
        $records = $this->prepareCandidatesRecords($candidate_selection_status);
        if ($records != null) {
            return $records->pluck('name', 'id');
        }
        return [null => 'No candidates found'];
    }

    /**
     * Update Proceed/Reject status of job application with feedback
     *
     * @param [type] $request
     * @return void
     */
    public function updateJobStatus($request)
    {
        try {
            \DB::beginTransaction();
            $this->candidateJobModel = $this->candidateJobModel->find($request->get('id'));
            $this->candidateJobModel->candidate_status = $request->get('candidate_status');
            $this->candidateJobModel->feedback_id = $request->get('feedback_id');
            $this->candidateJobModel->save();
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * To get the job application
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @return void
     */
    public function getJobApplicationOfCandidate($candidate_id, $job_id)
    {
        $candidateJob = $this->candidateJobModel->with([
            'job',
            'candidate',
            'candidate.attachements',
            'candidate.attachements.attachment',
            'candidate.addresses',
            'candidate.availability',
            'candidate.securityclearance',
            'candidate.guardingexperience',
            'candidate.force',
            'candidate.securityproximity',
            'candidate.wageexpectation',
            'candidate.experience',
            'candidate.miscellaneous',
            'candidate.employment_history',
            'candidate.references',
            'candidate.educations',
            'candidate.languages',
            'candidate.screening_questions',
            'candidate.skills',
            'candidate.termination',
            'candidate.personality_inventories.question',
            'candidate.personality_inventories.answer',
            'candidate.personality_sums',
            'candidate.personality_scores.score_type',
            'candidate.competency_matrix.competency_matrix',
            'candidate.competency_matrix.competency_matrix.category',
            'candidate.competency_matrix.competency_matrix_rating',
            'candidate.comissionaires_understanding',
        ])
            ->where('candidate_id', '=', $candidate_id)
            ->where('status', 'Applied')
            ->where('job_id', '=', $job_id)
            ->first();
        return $candidateJob;
    }

    /**
     * To get interview notes
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @return void
     */
    public function getInterviewNotesOfCandidate($candidate_id, $job_id)
    {
        return $this->candidateJobInterview->where([['job_id', $job_id], ['candidate_id', $candidate_id]])->first();
    }

    /**
     * Save HR tracking step
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @param [type] $request
     * @return void
     */
    public function saveHrTrackingStep($candidate_id, $job_id, $request)
    {
        try {
            \DB::beginTransaction();
            $user = \Auth::user();
            $candidateJob = $this->candidateJobModel->where('candidate_id', '=', $candidate_id)
                ->where('job_id', '=', $job_id);
            if ($candidateJob->count() > 0) {
                $completion_dates = $request->get('completion_date');
                $notes = $request->get('notes');
                $entered_by_ids = $request->get('entered_by_id');
                $candidateJobRecord = $candidateJob->first();
                if (is_array($completion_dates)) {
                    foreach ($completion_dates as $tracking_id => $completion_date) {
                        if (isset($completion_date) || isset($entered_by_ids[$tracking_id]) || isset($notes[$tracking_id])) {
                            if (!isset($completion_date)) {
                                return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["completion_date." . $tracking_id => ["Please select the date"]]], 422);
                            }
                            if (!isset($entered_by_ids[$tracking_id])) {
                                return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["entered_by_id." . $tracking_id => ["Please select the person"]]], 422);
                            }
                            $data['job_id'] = $job_id;
                            $data['candidate_id'] = $candidate_id;
                            $data['candidatejob_id'] = $candidateJobRecord->id;
                            $data['lookup_id'] = $tracking_id;
                            $data['completion_date'] = $completion_dates[$tracking_id];
                            $data['notes'] = isset($notes[$tracking_id]) ? $notes[$tracking_id] : '--';
                            $data['entered_by_id'] = $entered_by_ids[$tracking_id];
                            $this->candidateTracking->updateOrCreate(array('job_id' => $job_id, 'candidate_id' => $candidate_id, 'lookup_id' => $tracking_id), $data);
                        }
                    }
                }
                $candidateJob->update(['job_reassigned_id' => (int) $request->get('job_reassigned_id')]);
                \DB::commit();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Please select the candidate and his/her actually applied job and then Reassign']);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a tracking step
     *
     * @param [type] $job_id
     * @param [type] $step_id
     * @return void
     */
    public function deleteHrTracking($job_id, $candidate_id, $step_id)
    {
        try {
            \DB::beginTransaction();
            $this->candidateTracking->where('lookup_id', '=', $step_id)
                ->where('job_id', '=', $job_id)
                ->where('candidate_id', '=', $candidate_id)
                ->delete();
            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * To get schedule call logs
     *
     * @param [type] $candidate_id
     * @return void
     */
    public function getScheduleEventLogs($candidate_id)
    {
        return EventLogEntry::with('assignment_type', 'status_log')
            ->where('candidate_id', $candidate_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete candidate jobs/archive
     * @param type Request $request
     * @return boolean
     *
     */
    public function deleteCandidateJobs($request)
    {
        $candidate_ids = json_decode($request->get('candidate_ids'));
        if (!empty($candidate_ids)) {
            foreach ($candidate_ids as $id) {
                CandidateJob::where('candidate_id', $id)->delete();
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    /**
     * prepare candidate records for summary
     * @param tracking_summary
     * @return array
     *
     */
    public function prepareTrackingSummaryRecords($tracking_summary)
    {
        $datatable_rows = array();
        foreach ($tracking_summary as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["candidate_name"] = $each_record->name;
            $each_row["client_name"] = $each_record->latestJobApplied->job->customer->client_name;
            $each_row["project_number"] = $each_record->latestJobApplied->job->customer->project_number;

            $each_row["client_name1"] = (null != $each_record->latestJobApplied->reassigned_job) ? $each_record->latestJobApplied->reassigned_job->customer->client_name : '--';
            $each_row["project_number1"] = (null != $each_record->latestJobApplied->reassigned_job) ? $each_record->latestJobApplied->reassigned_job->customer->project_number : '--';
            $each_row["wage_low"] = $each_record->latestJobApplied->job->wage_low;
            $each_row["job_reassigned_id"] = $each_record->latestJobApplied->job_reassigned_id;
            $each_row["reassigned_job_wagelow"] = (null != $each_record->latestJobApplied->reassigned_job) ? $each_record->latestJobApplied->reassigned_job->wage_low : '--';
            $each_row["terminated"] = isset($each_record->termination);
            $candidate_track = $each_record->lastTrack;
            $arr_track = array();
            $key = 0;
            $arr_track[$key]['job_id'] = $candidate_track->job_id;
            $arr_track[$key]['candidate_id'] = $candidate_track->candidate_id;
            $arr_track[$key]['lookup_id'] = $candidate_track->lookup_id;
            $arr_track[$key]['completion_date'] = isset($candidate_track->completion_date) ? $candidate_track->completion_date : '--';
            $arr_track[$key]['notes'] = $candidate_track->notes;
            $arr_track[$key]['process_steps'] = (null != $candidate_track->tracking_process) ? $candidate_track->tracking_process->process_steps : '--';
            $arr_track[$key]['entered_by_id'] = $candidate_track->entered_by_id;
            $arr_track[$key]['first_name'] = isset($candidate_track->entered_by->first_name) ? $candidate_track->entered_by->first_name : '--';
            $arr_track[$key]['last_name'] = isset($candidate_track->entered_by->last_name) ? $candidate_track->entered_by->last_name : '--';
            $arr_track[$key]['full_name'] = $candidate_track->entered_by->first_name . " " . $candidate_track->entered_by->last_name;

            $arr_track[$key]['candidatejob_id'] = $candidate_track->candidatejob_id;
            $arr_track[$key]['step_number'] = (null != $candidate_track->tracking_process) ? $candidate_track->tracking_process->step_number : '--';
            $arr_track[$key]['job_reassigned_id'] = $candidate_track->job_reassigned_id;
            $latest_tracking_data = $arr_track[0];
            $combined_result = $each_row + $latest_tracking_data;
            array_push($datatable_rows, $combined_result);
        }

        return $datatable_rows;
    }

    /**
     * prepare candidate records for to be converted as employee
     * @param tracking_summary
     * @return array
     *
     */
    public function getCandidateConversionList()
    {
        $latest_record = $this->trackingProcessLookupModel->orderBy('step_number', 'desc')->first();
        $already_transitioned = CandidateEmployee::pluck('candidate_id')->toArray();
        $records = $this->candidateTracking->where('lookup_id', $latest_record->id)->with('candidate', 'job', 'tracking_process')->whereNotIn('candidate_id', $already_transitioned)->whereHas('candidatejob')->whereDoesntHave('candidate.termination')->get();
        $candidate_id_list = CandidateEmployee::pluck('candidate_id')->toArray();
        return $this->prepareDataArray($records, $candidate_id_list);
    }

    /**
     * prepare candidate records for conversion
     * @param tracking_summary
     * @return array
     *
     */
    public function prepareDataArray($records, $candidate_id_list)
    {
        $datatable_rows = array();
        foreach ($records as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["candidate_id"] = $each_record->candidate_id;
            $each_row["completion_date"] = $each_record->completion_date;
            $each_row["candidate_name"] = $each_record->candidate->name;
            $each_row["candidate_email"] = $each_record->candidate->email;
            $each_row["job_id"] = $each_record->job->unique_key;
            $each_row["status"] = in_array($each_record->candidate_id, $candidate_id_list) ? 0 : 1;
            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    /**
     * Function to get details of a single user
     * @param type $user_id
     */
    public function getCandidateDetails($candidate_id)
    {

        $user_details = $this->candidateJobModel->with('candidate.guardingExperience', 'candidate.miscellaneous', 'jobReassigned.customer', 'job.customer', 'candidate.wageExpectation')->where('candidate_id', $candidate_id)->where('status', 'Applied')->first();
        return $user_details;
    }

    public function candidateEmployeeMapping($user_store, $candidate_id)
    {
        $employee = new CandidateEmployee;
        $employee->candidate_id = $candidate_id;
        $employee->user_id = $user_store->id;
        $employee->updated_by = Auth::user()->id;
        $employee->save();
        return $employee->id;
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.candidate_employee'), $request->candidate_id);
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */

    public static function getRecruitmentAttachmentPathArr($request)
    {
        return array(config('globals.candidate_recruitment'), $request->candidate_id);
    }

    /**
     * Delete Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function deleteCandidateAttachment($candidate_id, $attachment_id)
    {
        try {
            CandidateAttachment::where(['candidate_id' => $candidate_id, 'attachment_id' => $attachment_id])->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function prepareCandidatesExcel($results, $customerLoc = null)
    {
        $result_arr = [];
        ////////////Header/////////////////////////
        $header_arr = array(
            "Client Name",
            "Candidate Name",
            "City",
            "Postal Code",
            "Wage Expectation",
            "Date Applied",
            "Email Address",
            "Phone",
            "Date of Birth",
            "Status",
            "Overall Impression",
            "Position Code",
            "Wage Per Hour",
            "Orientation",
            "Job Post Finding",
            "Sponsor Email",
            "Willingness to work on other positions",
            "How many hours a week are you looking for",
            "How soon could you start?",
            "Had you heard about Commissionaires?",
            "how familiar are you with Garda, G4S,Securitas or Palladin?",
            "Please share your understanding of Commissionaires PRIOR to applying ",
            "Please elaborate why you are applying for this specific role, and why you think you would succeed in the role",
            "Street Address",
            "Do you have valid security guarding licence in ontario with First Aid and CPR?",
            "Start date of Guarding Licence In Ontario",
            "Start date of First Aid Certificate",
            "Start date of CPR Certificate",
            "Expiry date of security guard licence",
            "Expiry date of First Aid certificate",
            "Expiry date of CPR certificate",
            "Ontario Security guard Test Score",
            "Do you have a valid security clearance ?",
            "Type of security clearance",
            "Expiry date of security clearance",
            "Do you have a valid Social Insurance Number in Canada?",
            "Do you have an expiry date on your SIN ?",
            "Expiry date of your SIN",
            "How many total years of security guarding experience do you have?",
            "What is the most senior position you have held in security?",
            "Years of experience in Access Control",
            "Years of experience in CCTV Operator",
            "Years of experience in Concierge",
            "Years of experience in Dispatch",
            "Years of experience in Foot Patrol",
            "Years of experience in Investigations",
            "Years of experience in Loss Prevention Officer",
            "Years of experience in Mobile Patrols",
            "Years of experience in Operations",
            "Years of experience in Security Guard",
            "Years of experience in Shift Leader",
            "Years of experience in Site Supervisor",
            "Years of experience in Other",
            "Wage expectations (From)",
            "Wage expectations (To)",
            "What was your last hourly wage within the security guarding industry?",
            "How many hours per week were you working at this wage?",
            "Can you validate your current wage with a paystup as evidence if we pay a higher wage?",
            "Who was the security provider that paid the wage?",
            "Name of the security provider that paid your previous wage",
            "Strengths of Securities",
            "What do you hope to get from Commissionaires that you feel Securitas was not providing?",
            "Rate your experience?",
            "What was your previous role?",
            "Explain your wage expectation. Why do you think you're worth the wage you are asking for?",
            "When you are available to start? ",
            "What is your current availability? ",
            "If only part time - please briefly explain your limitation",
            "Which days are you available to work?",
            "Which shifts are you willing to work?",
            "Do you understand the shift availability as noted above?",
            "Are you available for shift work including evenings and nights?",
            "If you answered 'no', please explain your restrictions:",
            "Were you born outside of Canada?",
            "Indicate your working status in Canada?",
            "How many years have you lived in Canada?",
            "Are you prepared to submit to a security screening?",
            "Do you have reason to believe you may NOT be granted a clearance?",
            "If you answered 'Yes', please explain:",
            "Do you have a valid drivers Licence?",
            "Do you have access to a vehicle?",
            "If you do not have a licence or access to a vehicle, do you have access to public transit?",
            "Does your method of transportation limit your availability?",
            "If you answered 'Yes', please explain",

        );

        $emp_header = $edu_header = $ref_header = $skill_header = array();
        $references_count = max(array_map('count', data_get($results, '*.references')));
        $employment_count = max(array_map('count', data_get($results, '*.employment_history')));
        $education_count = max(array_map('count', data_get($results, '*.educations')));
        $skills_obj = (data_get($results, '*.skills'));

        for ($i = 1; $i <= $employment_count; $i++) {
            array_push(
                $emp_header,
                'Employment Start Date' . $i,
                'Employment End Date' . $i,
                'Employment Employer' . $i,
                'Role' . $i,
                'Duties' . $i,
                'Reason' . $i
            );
        }

        for ($j = 1; $j <= $references_count; $j++) {
            array_push(
                $ref_header,
                'Name' . $j,
                'Employer' . $j,
                'Position' . $j,
                'Contact Phone' . $j,
                'Contact Email' . $j
            );
        }
        for ($k = 1; $k <= $education_count; $k++) {
            array_push(
                $edu_header,
                'Start Date' . $k,
                'End Date' . $k,
                'Grade' . $k,
                'Program' . $k,
                'School/Institute' . $k
            );
        }
        $lang_header = array(
            "Speaking (English)",
            "Reading (English)",
            "Writing (English)",
            "Speaking (French)",
            "Reading (French)",
            "Writing (French)",
        );
        $otherlang_header = array();
        for ($i = 1; $i < 6; $i++) {

            array_push($otherlang_header, "Language " . $i);
            array_push($otherlang_header, "Speaking ");
            array_push($otherlang_header, "Reading ");
            array_push($otherlang_header, "Writing ");
        }
        foreach ($skills_obj[0] as $key => $skills) {
            array_push($skill_header, $skills->skill_lookup->skills);
        }
        $other_header = array(
            'Please indicate if you have a Smartphone?',
            'If you have a smart phone what kind of phone is it?',
            'Rate your proficiency with using apps on your mobile phone?',
            'Are you a current employee of Commissionaires Great Lakes?',
            'Employee Number',
            'Currently Posted Site',
            'Position',
            'Hours/Week',
            'Have you ever applied for employment with Commissionaires Great Lakes?',
            'Start Date',
            'Currently Posted Site',
            'Position',
            'Have you ever been employed by the corps of Commissionaires?',
            'Position',
            'Start Date',
            'End Date',
            'Division',
            'Employee Number',
            'Are you a reservist/veteran of the Canadian Armed Forces, our allied forces, or RCMP?',
            'Service Number',
            'Candidate Force Branch or RCMP',
            'Enrollment Date',
            'Release Date',
            'Release Number',
            'Rank on Release',
            'Military Occupation',
            'Reason For Release',
            'Are you the spouse of someone who served in the Canadian Armed Forces',
            'Are you a native Indian/Indigenous person in Canada and hold an official Certificate of Indian Status?',
            'Have you ever been dismissed or asked to resign from employment?',
            'If you answered "Yes", please explain :',
            'Applicants must be psychologically healthy and should be capable of working alone for 24 hour rotating shifts.Do you have any limitations in these areas?',
            'If you answered "Yes", please explain',
            'Have you ever been convicted of a criminal offence for which you"ve not received a pardon?',
            'What was your offence?',
            'Date',
            'Location',
            'Disposition',
            'How would you describe your longer term career interests in security?',
            'Would you consider other roles with Commissionaires beyond what you"ve applied for?',
        );

        $candidateScoreHeaders = array(
            "Case Study Score",
            "Personality Score",
        );

        $competencyHeaders = array(
            "Adaptability",
            "Attention To Detail",
            "Collaboration",
            "Communication - Open",
            "Communication - Oral And Written",
            "Continuous Learning",
            "Crisis Management",
            "Judgement",
            "Diversity",
            "Drive For Results",
            "Initiative",
            "Innovation",
            "Negotiation",
            "Organizational Understanding",
            "Planning, Organization, and Time Management",
            "Problem Solving",
            "Professionalism",
            "Conflict Management",
            "Influence",
            "Team Leadership",
            "Quality",
            "Reliability",
            "Service",
            "Technical Expertise",
            "Change Leadership",
            "Coaching",
            "Collaborative Leadership",
        );

        $competencyHeadersOrderArr = array(
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 25, 26, 27, 18, 19, 20, 21, 22, 23, 24,
        );

        $drivingDistanceHeader = array(
            'Driving time in minutes',
        );

        $header_arr1 = array_merge(
            $header_arr,
            $emp_header,
            $ref_header,
            $edu_header,
            $lang_header,
            $otherlang_header,
            $skill_header,
            $other_header,
            $candidateScoreHeaders,
            $competencyHeaders
        );
        if (isset($customerLoc)) {
            $header_arr1 = array_merge($header_arr1, $drivingDistanceHeader);
        }

        $interviewScoreHeaderArr = array(
            "Interview Score",
            "Reference Score",
        );

        $header_arr1 = array_merge($header_arr1, $interviewScoreHeaderArr);

        $useOfForceHeaderArr = array(
            "Are you use of force certified?",
            "If yes, please provide your certification",
            "When does your certification expire?",
        );

        $header_arr1 = array_merge($header_arr1, $useOfForceHeaderArr);

        //////////////Header///////////////////////////
        foreach ($results as $key => $value) {
            $dist = [];
            $origins = array();
            $destinations = array();
            if (isset($customerLoc)) {
                $origins["lat"] = $value->geo_location_lat;
                $origins["long"] = $value->geo_location_long;
                $destinations["lat"] = $customerLoc->geo_location_lat;
                $destinations["long"] = $customerLoc->geo_location_long;
                $locationArr = array(
                    "origins" => [$origins],
                    "destinations" => [$destinations],
                );
                $distObj = $this->locationService
                    ->getDrivingDistance($locationArr, true);
                if ($distObj["status"]) {
                    $dist["time"] = $distObj["distanceMatrix"]->duration->value / 60; //converted to minutes
                } else {
                    $dist["time"] = "";
                }
            }

            $wage_position = 'Other';
            $guard_position = '';
            $wage_provider_other = '';
            $availability_ex = '';
            $availability_shift_ex = '';
            $clearance_ex = '';
            $recurity_transport_ex = '';

            $dec_position_ex = json_decode($value->guardingExperience->positions_experinces);
            //dd($dec_position_ex);
            $languare_obj = (data_get($value, 'languages'));
            $skills_obj = (data_get($value, 'skills'));
            $skill_lookups = ((data_get($skills_obj, '*.skill_lookup', 0)));
            $days = json_decode($value->availability->days_required) ?? [];
            $shift = json_decode($value->availability->shifts) ?? [];
            if ($value->guardingExperience->social_insurance_number != null && $value->guardingExperience->social_insurance_number == 1) {
                $social_no = 'Yes';
            } elseif ($value->guardingExperience->social_insurance_number != null && $value->guardingExperience->social_insurance_number == 0) {
                $social_no = 'No';
            } else {
                $social_no = '';
            }
            if ($value->guardingExperience->sin_expiry_date_status != null && $value->guardingExperience->sin_expiry_date_status == 1) {
                $sin_status = 'Yes';
            } elseif ($value->guardingExperience->sin_expiry_date_status != null && $value->guardingExperience->sin_expiry_date_status == 0) {
                $sin_status = 'No';
            } else {
                $sin_status = '';
            }
            if ($value->wageExpectation->last_role_held > 0) {
                $wage_position = $value->wageExpectation->lastrole->position ?? '';
            }
            if (isset($value->guardingExperience->most_senior_position_held)) {
                $guard_position = ($value->guardingExperience->most_senior_position_held > 0) ? $value->guardingExperience->position['position'] : 'Other';
            }
            if ($value->wageExpectation->wageprovider->security_provider == 'Other') {
                $wage_provider_other = $value->wageExpectation->wage_last_provider_other ?? '';
            }
            if ($value->availability->current_availability == "Part-Time (Less than 40 hours per week)") {
                $availability_ex = $value->availability->availability_explanation ?? '';
            }
            if ($value->availability->available_shift_work == "No") {
                $availability_shift_ex = $value->availability->explanation_restrictions ?? '';
            }
            if ($value->securityclearance->no_clearance == "Yes") {
                $clearance_ex = $value->securityclearance->no_clearance_explanation ?? '';
            }
            if ($value->securityproximity->transportation_limitted == "Yes") {
                $recurity_transport_ex = $value->securityproximity->explanation_transport_limit ?? '';
            }
            if ($value->referalAvailibility !== null && $value->referalAvailibility->orientation !== null) {
                $referalAvailablityOrientation = ($value->referalAvailibility->orientation != 0) ? 'Yes' : 'No';
            } else {
                $referalAvailablityOrientation = '';
            }

            $body_arr = array(
                $value->latestJobApplied->job->customer->client_name ?? '',
                $value->name ?? '',
                $value->city ?? '',
                $value->postal_code ?? '',
                '$' . $value->wageExpectation->wage_expectations_from . ' - $' . $value->wageExpectation->wage_expectations_to,
                $value->latestJobApplied->created_at ?? '',
                $value->email ?? '',
                $value->phone_home . ',' . $value->phone_cellular,
                $value->dob ?? '',
                ($value->latestJobApplied->candidate_status != '') ? $value->latestJobApplied->candidate_status : 'Not Set',
                $value->latestJobApplied->feedback->feedback ?? '',
                $value->latestJobApplied->job->unique_key ?? '',
                $value->latestJobApplied->job->wage_low . '-' . $value->latestJobApplied->job->wage_high,
                $referalAvailablityOrientation,
                $value->referalAvailibility->jobPostFinding->job_post_finding ?? '',
                $value->referalAvailibility->sponser_email ?? '',
                $value->referalAvailibility ? config('globals.position_availibility')[$value->referalAvailibility->position_availibility] : '',
                $value->referalAvailibility->floater_hours ?? '',
                $value->referalAvailibility ? config('globals.starting_time')[$value->referalAvailibility->starting_time] : '',
                $value->latestJobApplied->candidate_brand_awareness->answer ?? '',
                $value->latestJobApplied->candidate_security_awareness->answer ?? '',
                $value->comissionaires_understanding[0]->candidateUnderstandingLookup->commissionaires_understandings ?? '',
                $value->latestJobApplied->fit_assessment_why_apply_for_this_job ?? '',
                $value->address ?? '',
                $value->guardingExperience->guard_licence ?? '',
                $value->guardingExperience->start_date_guard_license ?? '',
                $value->guardingExperience->start_date_first_aid ?? '',
                $value->guardingExperience->start_date_cpr ?? '',
                $value->guardingExperience->expiry_guard_license ?? '',
                $value->guardingExperience->expiry_first_aid ?? '',
                $value->guardingExperience->expiry_cpr ?? '',
                $value->guardingExperience->test_score_percentage ?? '',
                $value->guardingExperience->security_clearance ?? '',
                $value->guardingExperience->security_clearance_type ?? '',
                $value->guardingExperience->security_clearance_expiry_date ?? '',
                $social_no,
                $sin_status,
                $value->guardingExperience->sin_expiry_date ?? '',
                $value->guardingExperience->years_security_experience ?? '',
                $guard_position ?? 'Other',
                $dec_position_ex->access_control ?? '',
                $dec_position_ex->cctv_operator ?? '',
                $dec_position_ex->concierge ?? '',
                $dec_position_ex->dispatch ?? '',
                $dec_position_ex->foot_patrol ?? '',
                $dec_position_ex->investigations ?? '',
                $dec_position_ex->loss_prevention_officer ?? '',
                $dec_position_ex->mobile_patrols ?? '',
                $dec_position_ex->operations ?? '',
                $dec_position_ex->security_guard ?? '',
                $dec_position_ex->shift_leader ?? '',
                $dec_position_ex->site_supervisor ?? '',
                $dec_position_ex->other ?? '',
                $value->wageExpectation->wage_expectations_from ?? '',
                $value->wageExpectation->wage_expectations_to ?? '',
                $value->wageExpectation->wage_last_hourly ?? '',
                $value->wageExpectation->wage_last_hours_per_week ?? '',
                $value->wageExpectation->current_paystub ?? '',
                $value->wageExpectation->wageprovider->security_provider ?? '',
                $wage_provider_other ?? '',
                $value->wageExpectation->security_provider_strengths ?? '',
                $value->wageExpectation->security_provider_notes ?? '',
                $value->wageExpectation->rating->experience_ratings ?? '',
                $wage_position ?? 'Other',
                $value->wageExpectation->explanation_wage_expectation ?? '',
                $value->availability->availability_start ?? '',
                $value->availability->current_availability ?? '',
                $availability_ex ?? '',
                implode(",", $days),
                implode(",", $shift),
                $value->availability->understand_shift_availability ?? '',
                $value->availability->available_shift_work ?? '',
                $availability_shift_ex ?? '',
                $value->securityclearance->born_outside_of_canada ?? '',
                $value->securityclearance->work_status_in_canada ?? '',
                $value->securityclearance->years_lived_in_canada ?? '',
                $value->securityclearance->prepared_for_security_screening ?? '',
                $value->securityclearance->no_clearance ?? '',
                $clearance_ex ?? '',
                $value->securityproximity->driver_license ?? '',
                $value->securityproximity->access_vehicle ?? '',
                $value->securityproximity->access_public_transport ?? '',
                $value->securityproximity->transportation_limitted ?? '',
                $recurity_transport_ex ?? '',

            );

            ////// Employment History ////////
            $emp_arr = array();

            foreach ($value->employment_history as $key => $emp) {
                array_push(
                    $emp_arr,
                    $emp->start_date,
                    $emp->end_date,
                    $emp->employer,
                    $emp->role,
                    $emp->duties,
                    $emp->reason
                );
            }
            $total_count = count($value->employment_history);
            for ($total_count; $total_count < $employment_count; $total_count++) {
                array_push(
                    $emp_arr,
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                );
            }
            ////// Employment History ////////

            ////// Reference ////////
            $ref_arr = array();
            foreach ($value->references as $key => $ref) {
                array_push(
                    $ref_arr,
                    $ref->reference_name,
                    $ref->reference_employer,
                    $ref->reference_position,
                    $ref->contact_phone,
                    $ref->contact_email
                );
            }
            $total_ref_count = count($value->references);
            for ($total_ref_count; $total_ref_count < $references_count; $total_ref_count++) {
                array_push(
                    $ref_arr,
                    '',
                    '',
                    '',
                    '',
                    ''
                );
            }
            ////// Reference ////////

            ////// Education ////////
            $edu_arr = $skill_arr = array();
            $k = 1;
            foreach ($value->educations as $key => $edu) {
                array_push(
                    $edu_arr,
                    $edu->start_date_education,
                    $edu->end_date_education,
                    $edu->grade,
                    $edu->program,
                    $edu->school
                );
            }
            $total_edu_count = count($value->educations);
            for ($total_edu_count; $total_edu_count < $education_count; $total_edu_count++) {
                array_push(
                    $edu_arr,
                    '',
                    '',
                    '',
                    '',
                    ''
                );
            }
            ////// Education ////////

            $language_arr = array(
                ($languare_obj[0]->language_id == 1) ? $languare_obj[0]->speaking : '',
                ($languare_obj[0]->language_id == 1) ? $languare_obj[0]->reading : '',
                ($languare_obj[0]->language_id == 1) ? $languare_obj[0]->writing : '',
                ($languare_obj[1]->language_id == 2) ? $languare_obj[1]->speaking : '',
                ($languare_obj[1]->language_id == 2) ? $languare_obj[1]->reading : '',
                ($languare_obj[1]->language_id == 2) ? $languare_obj[1]->writing : '',
            );
            foreach ($skills_obj as $skills) {
                array_push($skill_arr, $skills->skill_level);
            }

            $other_arr = array(
                $value->smart_phone_type_id ? 'Yes' : 'No',
                $value->technicalSummary->type ?? '',
                $value->smart_phone_skill_level ?? '',
                $value->experience->current_employee_commissionaries ?? '',
                $value->experience->employee_number ?? '',
                $value->experience->currently_posted_site ?? '',
                $value->experience->position ?? '',
                $value->experience->hours_per_week ?? '',
                $value->experience->applied_employment ?? '',
                $value->experience->start_date_position_applied ?? '',
                $value->experience->end_date_position_applied ?? '',
                $value->experience->position_applied ?? '',
                $value->experience->employed_by_corps ?? '',
                $value->experience->position_employed ?? '',
                $value->experience->start_date_employed ?? '',
                $value->experience->end_date_employed ?? '',
                $value->experience->division->division_name ?? '',
                $value->experience->employee_num ?? '',
                $value->miscellaneous->veteran_of_armedforce ?? '',
                $value->miscellaneous->service_number ?? '',
                $value->miscellaneous->canadian_force ?? '',
                $value->miscellaneous->enrollment_date ?? '',
                $value->miscellaneous->release_date ?? '',
                $value->miscellaneous->item_release_number ?? '',
                $value->miscellaneous->rank_on_release ?? '',
                $value->miscellaneous->military_occupation ?? '',
                $value->miscellaneous->reason_for_release ?? '',
                $value->miscellaneous->spouse_of_armedforce ?? '',
                $value->miscellaneous->is_indian_native ?? '',
                $value->miscellaneous->dismissed ?? '',
                $value->miscellaneous->explanation_dismissed ?? '',
                $value->miscellaneous->limitations ?? '',
                $value->miscellaneous->limitation_explain ?? '',
                $value->miscellaneous->criminal_convicted ?? '',
                $value->miscellaneous->offence ?? '',
                $value->miscellaneous->offence_date ?? '',
                $value->miscellaneous->offence_location ?? '',
                $value->miscellaneous->disposition_granted ?? '',
                $value->miscellaneous->career_interest ?? '',
                $value->miscellaneous->other_roles ?? '',

            );

            // Average screening questions score
            $scoreArr = array();
            $scoreArr[] = $value->latestJobApplied->average_score;
            $scoreArr[] = isset($value->personality_scores[0])
            ? $value->personality_scores[0]->score
            : "";

            // Competency matrix rating
            $competencyArr = array();
            foreach ($competencyHeadersOrderArr as $competencyId) {
                $competencyArr[] =
                $value->competency_matrix
                    ->where('competency_matrix_lookup_id', $competencyId)
                    ->first()->competency_matrix_rating->rating;
            }
            $otherLanguageArray = $value->other_languages;
            $otherLanguageArrayVal = [];
            if ($otherLanguageArray != null) {
                $languagecollection = $otherLanguageArray->count();
                foreach ($otherLanguageArray as $otherlang) {
                    array_push($otherLanguageArrayVal, $otherlang->language_lookup->language);
                    array_push($otherLanguageArrayVal, $otherlang->speaking);
                    array_push($otherLanguageArrayVal, $otherlang->reading);
                    array_push($otherLanguageArrayVal, $otherlang->writing);
                }
                for ($i = $languagecollection + 1; $i < 6; $i++) {
                    array_push($otherLanguageArrayVal, " ");
                    array_push($otherLanguageArrayVal, " ");
                    array_push($otherLanguageArrayVal, " ");
                    array_push($otherLanguageArrayVal, " ");
                }
            } else {
                for ($i = 1; $i < 6; $i++) {
                    array_push($otherLanguageArrayVal, " ");
                    array_push($otherLanguageArrayVal, " ");
                    array_push($otherLanguageArrayVal, " ");
                    array_push($otherLanguageArrayVal, " ");
                }
            }

            if (isset($customerLoc)) {
                $drivingArr = array($dist['time']);
            }

            $interviewScoreArr = array(
                $value->latestJobApplied->interview_score ?? "",
                $value->latestJobApplied->reference_score ?? "",
            );

            $useOfForceArr = array(
                $value->force->force ?? '',
                $value->force->force_lookup->use_of_force ?? '',
                $value->force->expiry ?? '',
            );

            $res_arr1 = array_merge(
                $body_arr,
                $emp_arr,
                $ref_arr,
                $edu_arr,
                $language_arr,
                $otherLanguageArrayVal,
                $skill_arr,
                $other_arr,
                $scoreArr,
                $competencyArr
            );
            if (isset($customerLoc)) {
                $res_arr1 = array_merge($res_arr1, $drivingArr);
            }
            $res_arr1 = array_merge($res_arr1, $interviewScoreArr);
            $res_arr1 = array_merge($res_arr1, $useOfForceArr);
            $final_arr[] = $res_arr1;
        }

        $final_header[] = $header_arr1;
        $result_arr = array_merge($final_header, $final_arr);
        return $result_arr;
    }

    public function getListOfHiredCandidate($inputs)
    {
        $inputs = $this->helperService->getFMDashboardFilters();

        $getList = CandidateTracking::has('candidate')->where('lookup_id', '=', '18')
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {
                    //For From date
                    if (!empty($inputs['from_date'])) {
                        $query->where('created_at', '>=', $inputs['from_date']);
                    }
                    //For to date
                    if (!empty($inputs['to_date'])) {
                        $query->where('created_at', '<=', $inputs['to_date']);
                    }

                    //For customer_ids
                    $query->whereHas('job', function ($q) use ($inputs) {
                        $q->whereIn('customer_id', $inputs['customer_ids']);
                    });
                }
            })
            ->get();
        $getLists = $getList->count();
        return $getLists;
    }

    public function getListOfIntransitCandidate($inputs)
    {
        $intransit_count = [];
        $inputs = $this->helperService->getFMDashboardFilters();

        $getList = CandidateTracking::has('candidate')->selectRaw('candidate_id, max(lookup_id) as max_lookup_id')
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {
                    //For From date
                    if (!empty($inputs['from_date'])) {
                        $query->where('created_at', '>=', $inputs['from_date']);
                    }
                    //For to date
                    if (!empty($inputs['to_date'])) {
                        $query->where('created_at', '<=', $inputs['to_date']);
                    }

                    //For customer_ids
                    $query->whereHas('job', function ($q) use ($inputs) {
                        $q->whereIn('customer_id', $inputs['customer_ids']);
                    });
                }
            })->groupBy('candidate_id')
            ->get();

        $intransit_count = Arr::where(data_get($getList, '*.max_lookup_id'), function ($value, $key) {
            return $value < 18;
        });

        return sizeof($intransit_count);
    }

    public function getListOfPendingCandidate($inputs)
    {

        $inputs = $this->helperService->getFMDashboardFilters();

        $getList = CandidateJob::has('candidate')->doesntHave('candidateTracking')
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {
                    //For From date
                    if (!empty($inputs['from_date'])) {
                        $query->where('created_at', '>=', $inputs['from_date']);
                    }
                    //For to date
                    if (!empty($inputs['to_date'])) {
                        $query->where('created_at', '<=', $inputs['to_date']);
                    }

                    //For customer_ids
                    $query->whereHas('job', function ($q) use ($inputs) {
                        $q->whereIn('customer_id', $inputs['customer_ids']);
                    });
                }
            })->where(function ($q) {
            $q->where('candidate_status', null)
                ->orWhere('candidate_status', 'Proceed');
        })->get();

        $getLists = $getList->count();
        return $getLists;
    }

    /**
     * Get the path including file name to incident report attachment
     * @param $incident_report_id
     * @return string
     */
    public function testScoreAttachment($file_id)
    {
        $path = array();
        $candidate_attachment = CandidateSecurityGuardingExperince::where('test_score_document_id', $file_id)->first();
        $candidate_id = $candidate_attachment->candidate_id;
        $file_name = $candidate_attachment->testScoreAttachmentDetails->hash_name;
        if (!empty($candidate_attachment->test_score_document_id)) {
            $path['path'] = storage_path('app') . $this->directory_seperator . config('globals.candidate_recruitment') . $this->directory_seperator . $candidate_id . $this->directory_seperator . $file_name;
            $path['file'] = $file_name;
        }

        return $path;
    }

    /**
     * Get the path including file name to incident report attachment
     * @param $incident_report_id
     * @return string
     */
    public function forceAttachment($file_id)
    {
        $path = array();
        $force_attachment = CandidateForceCertification::where('attachment_id', $file_id)->first();
        $candidate_id = $force_attachment->candidate_id;
        $file_name = $force_attachment->forceAttachmentDetails->hash_name;
        if (!empty($force_attachment->attachment_id)) {
            $path['path'] = storage_path('app') . $this->directory_seperator . config('globals.candidate_recruitment') . $this->directory_seperator . $candidate_id . $this->directory_seperator . $file_name;
            $path['file'] = $file_name;
        }
        return $path;
    }

    public function distanceWithTimeByPositionCoordinates($originStr, $destinationStr)
    {
        $result = ['distance' => '-', 'duration' => '-'];
        $apiKey = config('globals.google_api_curl_key');
        \App\Services\HelperService::googleAPILog('distancematrix', '\Modules\Hranalytics\Repositories\CandidateRepository::distanceWithTimeByPositionCoordinates');
        $location_data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $originStr . "&destinations=" . $destinationStr . "&mode=driving&key=" . $apiKey);
        $response = json_decode($location_data);
        if (!empty($response) && isset($response->rows[0]) && isset($response->rows[0]->elements[0])) {
            $matrixData = $response->rows[0]->elements[0];
            if (!empty($matrixData) && isset($matrixData->distance) && isset($matrixData->duration)) {
                $result = [
                    'distance' => $matrixData->distance->text,
                    'duration' => $matrixData->duration->text,
                ];
            }
        }

        return $result;
    }
}
