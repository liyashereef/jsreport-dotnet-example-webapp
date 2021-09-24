<?php

namespace Modules\Recruitment\Repositories;

use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Modules\Admin\Models\DivisionLookup;
use Modules\Admin\Models\PositionLookup;
use Modules\Admin\Models\SecurityProviderLookup;
use Modules\Admin\Models\SkillLookup;
use Modules\Admin\Models\SmartPhoneType;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Recruitment\Models\RecBrandAwareness;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Models\RecCandidateAttachment;
use Modules\Recruitment\Models\RecCandidateAwareness;
use Modules\Recruitment\Models\RecCandidateEmployee;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Recruitment\Models\RecCandidateScreeningQuestion;
use Modules\Recruitment\Models\RecCandidateTracking;
use Modules\Recruitment\Models\RecCommissionairesUnderstandingLookup;
use Modules\Recruitment\Models\RecProcessSteps;
use Modules\Recruitment\Models\RecRateExperienceLookups;
use Modules\Recruitment\Models\RecSecurityAwareness;
use Modules\LearningAndTraining\Repositories\EmployeeAllocationRepository;
use Modules\LearningAndTraining\Models\TrainingUser;
use Modules\Recruitment\Models\RecCandidateForceCertification;
use App\Jobs\RecMatchScoreCandidate;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Recruitment\Models\RecJob;
use Log;
use File;
use PDF;
use Carbon;
use App\Repositories\AttachmentRepository;
use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;

class RecCandidateRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $candidateModel, $recCandidateAwarenessModel, $recCandidateTracking;

    /**
     * Create a new CandidateRepository instance.
     *
     * @param \App\Models\Candidate $candidate
     */
    public function __construct(
        RecCandidate $candidateModel,
        RecCandidateAwareness $recCandidateAwarenessModel,
        RecCandidateTracking $recCandidateTracking,
        HelperService $helperService,
        MailQueueRepository $mailQueueRepository,
        RecCandidateTrackingRepository $recCandidateTrackingRepository,
        RecCandidateJobDetails $recCandidateJobDetails,
        EmployeeAllocationRepository $employeeAllocationRepository,
        AttachmentRepository $attachmentRepo,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->directory_seperator = "/";
        $this->candidateModel = $candidateModel;
        $this->recCandidateAwarenessModel = $recCandidateAwarenessModel;
        $this->recCandidateTracking = $recCandidateTracking;
        $this->helperService = $helperService;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->recCandidateTrackingRepository = $recCandidateTrackingRepository;
        $this->recCandidateJobDetails = $recCandidateJobDetails;
        $this->employeeAllocationRepository=$employeeAllocationRepository;
        $this->attachmentRepo=$attachmentRepo;
        $this->customerEmployeeAllocationRepository=$customerEmployeeAllocationRepository;
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
                    $query->whereHas('competencyTracking', function ($query) use ($request) {
                        $query->when((($request != null) && $request->has('application_date') && !empty($request->get('application_date'))), function ($query) use ($request) {
                            $applicationStartDate = date("Y-m-d H:i:s", strtotime($request->get('application_date') . " 00:00:00"));
                            $applicationEndDate = date("Y-m-d H:i:s", strtotime($request->get('application_date') . " 23:59:59"));
                            if ($request->get('application_date_condition') == "=") {
                                $query->whereBetween('completed_date', [$applicationStartDate, $applicationEndDate]);
                            } elseif ($request->get('application_date_condition') == "<=") {
                                $query->where('completed_date', "<=", $applicationEndDate);
                            } else {
                                $query->where('completed_date', ">=", $applicationStartDate);
                            }
                        });
                    });
                }
                if (null !== ($request->get('training_completed')) && $request->get('training_completed') == "No") {
                    $candidateStage[] = config('globals.training_completed_tracking_id');
                    if (!empty($candidateStage)) {
                        $query->whereDoesntHave('tracking', function ($query) use ($candidateStage) {
                            $query->when((!empty($candidateStage)), function ($query) use ($candidateStage) {
                                $query->where('process_lookups_id', $candidateStage);
                            });
                        });
                    }
                }
                if (null !== ($request->get('training_completed')) && $request->get('training_completed') == "Yes") {
                    $candidateStage[] = config('globals.training_completed_tracking_id');
                    if (!empty($candidateStage)) {
                        $query->whereHas('tracking', function ($query) use ($candidateStage) {
                            $query->when((!empty($candidateStage)), function ($query) use ($candidateStage) {
                                $query->where('process_lookups_id', $candidateStage);
                            });
                        });
                    }
                }
                //candidate stage based filtering


                if (null !== ($request->get('candidate_stage'))) {
                    $candidateStage = array_filter($request->get('candidate_stage'));
                    if (!empty($candidateStage)) {
                        $query->whereHas('lastTrack', function ($query) use ($candidateStage) {
                            $query->when((!empty($candidateStage)), function ($query) use ($candidateStage) {
                                $query->whereIn('process_lookups_id', $candidateStage);
                            });
                        });
                    }
                }




                //candidate personality score
                if (null !== ($request->get('personality_type'))) {
                    $query->whereHas('personality_scores', function ($query) use ($request) {
                        $query->when((($request != null) && $request->has('personality_type') && !empty($request->get('personality_type'))), function ($query) use ($request) {
                            $query->where('score', 'like', $request->get('personality_type'))->where('order', 1);
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

        // if (null !== ($request->get('candidate_score'))) {
        ->whereHas('awareness', function ($query) use ($candidate_selection_status, $user, $request) {
          //  $query->where('status', '=', 'Applied')
               $query->when($candidate_selection_status != null, function ($query) use ($candidate_selection_status) {
                $query->where('candidate_status', '=', $candidate_selection_status);
               });
            if (null != $request && null !== ($request->get('candidate_score'))) {
                $query->where('average_score', $request->get('candidate_score_condition'), $request->get('candidate_score'));
            }
        });
       // }

        // ->when(
        //     $type_of_records_request == null,
        //     function ($query) use ($user, $request, $customer_session) {
            // $query->whereHas('latestJobApplied.job', function ($query) use ($user, $request, $customer_session) {

        //             $query->when(!$user->hasAnyPermission(['view_all_candidates', 'admin', 'super_admin']), function ($query) use ($user) {
            //         //$query->where('user_id', '=', $user->id);
            //         //$query->when($user->can('hr-tracking'), function ($query) use ($user) {
            //         $query->where('hr_rep_id', '=', $user->id)
            //             ->orWhere('user_id', '=', $user->id);
            //         //});
            //     });

            //     if (null != $request && null !== ($request->get('job_applied'))) {
            //         $query->where('customer_id', '=', $request->get('job_applied'));
            //     }

            //     /** START ** Get Customer Ids from Session and Filter */
            //     if ($customer_session) {
            //         $customer_ids = $this->helperService->getCustomerIds();
            //         if (!empty($customer_ids)) {
            //             $query->whereIn('customer_id', $customer_ids);
            //         }
            //     }
            //     /** END ** Get Customer Ids from Session and Filter */
            // });
        //     },
        //     * START ** when $type_of_records_request not is_null..used for candidate tracking page if track all customer permission is given /*
        //     function ($query) use ($user, $request, $customer_session) {

            // $query->whereHas('latestJobApplied.job', function ($query) use ($user, $request, $customer_session) {

        //             $query->when(!$user->hasAnyPermission(['track_all_candidates', 'view_all_candidates_candidate_onboardingstatus', 'admin', 'super_admin']), function ($query) use ($user) {
            //         //$query->where('user_id', '=', $user->id);
            //         //$query->when($user->can('hr-tracking'), function ($query) use ($user) {
            //         $query->where('hr_rep_id', '=', $user->id)
            //             ->orWhere('user_id', '=', $user->id);
            //         //});
            //     });

            //     if (null != $request && null !== ($request->get('job_applied'))) {
            //         $query->where('customer_id', '=', $request->get('job_applied'));
            //     }

            //     /** START ** Get Customer Ids from Session and Filter */
            //     if ($customer_session) {
            //         $customer_ids = $this->helperService->getCustomerIds();
            //         if (!empty($customer_ids)) {
            //             $query->whereIn('customer_id', $customer_ids);
            //         }
            //     }
            //     /** END ** Get Customer Ids from Session and Filter */
            // });
        //     }
        // )
        /** End **  when $type_of_records_request not is_null..used for candidate tracking page if track all customer permission is given */

        $query->when($request != null, function ($query) use ($request) {

            if (null !== ($request->get('location'))) {
                $query->where('city', 'like', "%" . $request->get('location') . "%");
            }
            if (null !== ($request->get('availability'))) {
                $query->whereHas('availability', function ($query) use ($request) {
                    $query->where('current_availability', '=', $request->get('availability'));
                });
            }
            if (null !== ($request->get('wage_low'))  || null !== ($request->get('current_wage'))) {
                $query->whereHas('wageExpectation', function ($query) use ($request) {
                    if (null !== ($request->get('wage_low'))) {
                        $query->where('wage_expectations', $request->get('wage_low_condition'), $request->get('wage_low'));
                    }
                    // if (null !== ($request->get('wage_high'))) {
                    //     $query->where('wage_expectations_to', $request->get('wage_high_condition'), $request->get('wage_high'));
                    // }
                    if (null !== ($request->get('current_wage'))) {
                        $query->where('wage_last_hourly', $request->get('current_wage_condition'), $request->get('current_wage'));
                    }
                });
            }

            if ((null !== ($request->get('years_experience')))
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
            // dd($query->get(), $request->all());

            if ((null !== ($request->get('years_canada')))
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

            if ((null !== ($request->get('drivers_license')))) {
                // drivers license filter
                $query->whereHas('securityproximity', function ($query) use ($request) {
                    if (null !== ($request->get('drivers_license'))) {
                        $query->where('driver_license', $request->get('drivers_license'));
                    }
                });
            }

            if ((null !== ($request->get('use_of_force')))) {
                //use of force filter
                $query->whereHas('force', function ($query) use ($request) {
                    if (null !== ($request->get('use_of_force'))) {
                        $query->where('force', $request->get('use_of_force'));
                    }
                });
            }

            if ((null !== ($request->get('vet')))
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
            if ((null !== ($request->get('orientation')))
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
           // 'employment_history_latest',
         'references',
        // 'educations',
        'languages',
        // 'skills.skill_lookup',
        // 'technicalSummary',
        'experience',

           'candidateJobs',             'awareness',
        // 'experience.division',
        'miscellaneous',
        'lastTrack',
        // 'trackings',
         'tracking.tracking_process',
        // 'trackings.entered_by',
        // 'lastTrack.tracking_process',
        // 'lastTrack.entered_by',
        // 'termination',
            ])
            ->with(['candidateJobs' => function ($q) use ($request) {
            // Query the name field in status table
                $q->where('job_id', '=', $request->job_id); // '=' is optional
            }])
         ->when(($user->hasPermissionTo('rec-view-allocated-candidates-geomapping')) &&(!\Auth::user()->hasAnyPermission([
            "super_admin","admin"])), function ($query) {
                $customer_list=$this->customerEmployeeAllocationRepository->getDirectAllocatedCustomersList(\Auth::user());
                $jobList=RecJob::whereIn('customer_id', array_keys($customer_list))->pluck('id')->toArray();
                $query->whereHas('candidateJobs', function ($q) use ($jobList) {
                       $q->whereIn('job_id', $jobList)->where('status', 3);
                });
            })
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
      
        $query->where('review_completed', 1)->whereHas('guardingExperience')
            ->whereHas('wageExpectation');
        if (!empty($candidateIdArr)) {
            $query->whereIn('id', $candidateIdArr);
        }
         
        if ($widgetRequest) {
            $count = (int)config('dashboard.candidate_screening_summary_row_limit');
            $query->limit($count);
        }

        return $query;
    }


    public function getCandidateSummaryList()
    {
        $query = $this->candidateModel
        ->where('is_completed', 1)
        ->whereHas('guardingExperience')
        ->whereHas('wageExpectation')
        ->with([
        //'latestJobApplied.job.customer',
        // 'latestJobApplied.job.positionBeeingHired',
        // 'latestJobApplied.job.reason',
        // 'latestJobApplied.job.assignmentType',
        // 'latestJobApplied.feedback',
        // 'latestJobApplied.reassigned_job',
        // 'latestJobApplied.reassigned_job.customer',
        // 'latestJobApplied.job.assignee',
        // 'latestJobApplied.candidate_brand_awareness',
        // 'latestJobApplied.candidate_security_awareness',
        // 'comissionaires_understanding.candidateUnderstandingLookup',
        'guardingExperience',
        'wageExpectation',
        'candidateJobs',
        'awareness.feedback',
        // 'wageExpectation.rating',
        // 'wageExpectation.lastrole',
        // 'wageExpectation.wageprovider',
        // 'availability',
        // 'securityproximity',
        // 'employment_history',
        // 'references',
        // 'educations',
        // 'languages',
        // 'skills.skill_lookup',
        // 'technicalSummary',
        // 'experience',
        // 'experience.division',
        // 'miscellaneous',

        // 'trackings',
        // 'trackings.tracking_process',
        // 'trackings.entered_by',
        'lastTrack.tracking_process',
        // 'lastTrack.entered_by',
        // 'termination',
        ])
        ->when((\Auth::user()->hasPermissionTo('rec-view-allocated-candidates-summary')) && !\Auth::user()->hasAnyPermission([
            "super_admin", "admin" ]), function ($query) {
                $customer_list=$this->customerEmployeeAllocationRepository->getDirectAllocatedCustomersList(\Auth::user());
                $jobList=RecJob::whereIn('customer_id', array_keys($customer_list))->pluck('id')->toArray();
                $excludeQuery=clone $query;
                $alreadyOnboardedForUnallocated=$excludeQuery->whereHas('candidateJobs', function ($q) use ($jobList) {
                       $q->whereNotIn('job_id', $jobList)->where('status', 3);
                })->get()->pluck('id')->toArray();
                $query->whereHas('candidateJobs', function ($q) use ($jobList, $alreadyOnboardedForUnallocated) {
                       $q->whereIn('job_id', $jobList)->whereNotNull('rec_preference')->whereNotIn('candidate_id', $alreadyOnboardedForUnallocated);
                });
            })
        ->orderBy('name')->get();

        return $this->preparedSummaryArray($query);
    }


    public function preparedSummaryArray($data)
    {
        $arr = $datatable_rows = array();
        foreach ($data as $key => $each_data) {
            $arr['id'] = $each_data->id;
            $arr['name'] = $each_data->name;
            $arr['profile_image'] = $each_data->profile_image ?? '--';
            $arr['city'] = $each_data->city;
            $arr['postal_code'] = $each_data->postal_code;
            $arr['years_security_experience'] = $each_data->guardingExperience->years_security_experience;
            $arr['last_wage'] = $each_data->wageExpectation->wage_last_hourly;
            $arr['wage_expectations'] = $each_data->wageExpectation->wage_expectations;
            $arr['candidate_status'] = $each_data->awareness->candidate_status ?? 'Not Set';
            $arr['awareness_id'] = $each_data->awareness->id ?? '--';
            $arr['awareness_average_score'] = $each_data->awareness->average_score ?? '--';
            $arr['email'] = $each_data->email;
            $arr['feedback'] = $each_data->awareness->feedback->feedback ?? '--';
            $arr['termination'] = $each_data->termination;
            $arr['phone_cellular'] = $each_data->phone_cellular;
            $arr['tracking'] = $each_data->lastTrack;
            $arr['application_date'] = Carbon::parse($each_data->competencyTracking->completed_date)->format('d-m-Y');
            $arr['application_date_unformatted'] = $each_data->competencyTracking->completed_date;
            //$arr['tracking_name']=$each_data->lastTrack->tracking_process->display_name;
            $arr['phone'] = $each_data->phone;
            $arr['loginTracking'] = Carbon::parse($each_data->loginTracking->completed_date)->format('d-m-Y');
            $arr['cycle_time'] = Carbon::parse($each_data->competencyTracking->completed_date)->diffInDays($each_data->loginTracking->completed_date);
            if ($each_data->lastTrack->process_lookups_id == config('globals.training_completed_tracking_id')) {
                $secondLastTrackingStep = config('globals.training_completed_tracking_id') - 1;
                if ($each_data->tracking->slice(1)->first()->process_lookups_id == $secondLastTrackingStep) {
                    $arr['tracking_name'] = $each_data->lastTrack->tracking_process->display_name ?? '--';
                    $arr['tracking_step_id'] = $each_data->lastTrack->tracking_process->id;
                } else {
                    $arr['tracking_name'] = $each_data->tracking->slice(1)->first()->tracking_process->display_name ?? '--';
                    $arr['tracking_step_id'] = $each_data->tracking->slice(1)->first()->tracking_process->id;
                }
            } else {
                $arr['tracking_name'] = $each_data->lastTrack->tracking_process->display_name ?? '--';
                $arr['tracking_step_id'] = $each_data->lastTrack->tracking_process->id;
            }
            array_push($datatable_rows, $arr);
        }
        // dd($datatable_rows);
        return $datatable_rows;
    }

    /**
     * To get candidate records
     *
     * @param [type] $candidate_selection_status
     * @return void
     */
    public function getCandidates($candidate_selection_status = null, $request = null, $type_of_records_request = null, $order_by = 'name', $customer_session = false, $widgetRequest = false, $candidateIdArr = null)
    {
        $records = $this->prepareCandidatesRecords($candidate_selection_status, $request, $type_of_records_request, $order_by, $customer_session, $widgetRequest, $candidateIdArr)->get();
        return $records;
    }




    public function getCandidateTrainingList()
    {
        $query = $this->candidateModel
            ->where('is_completed', 1)
            ->whereHas('guardingExperience')
            ->whereHas('wageExpectation')
            ->with([
                'guardingExperience',
                'wageExpectation',
                'candidateJobs',
                'awareness.feedback',
                'lastTrack.tracking_process',
            ])
            ->orderBy('name')->get();
        return $this->preparedCandidateTrainingArray($query);
    }


    public function preparedCandidateTrainingArray($data)
    {
        $arr = $datatable_rows = array();
        foreach ($data as $key => $each_data) {
            $arr['id'] = $each_data->id;
            $arr['name'] = $each_data->name;
            $arr['profile_image'] = $each_data->profile_image ?? '--';
            $arr['city'] = $each_data->city;
            $arr['postal_code'] = $each_data->postal_code;
            $arr['years_security_experience'] = $each_data->guardingExperience->years_security_experience;
            $arr['last_wage'] = $each_data->wageExpectation->wage_last_hourly;
            $arr['candidate_status'] = $each_data->awareness->candidate_status ?? 'Not Set';
            $arr['awareness_id'] = $each_data->awareness->id ?? '--';
            $arr['awareness_average_score'] = $each_data->awareness->average_score ?? '--';
            $arr['email'] = $each_data->email;
            $arr['feedback'] = $each_data->awareness->feedback->feedback ?? '--';
            $arr['termination'] = $each_data->termination;
            $arr['phone_cellular'] = $each_data->phone_cellular;
            $arr['tracking'] = $each_data->lastTrack;
            $arr['application_date'] = Carbon::parse($each_data->competencyTracking->completed_date)->format('d-m-Y');
            $arr['application_date_unformatted'] = $each_data->competencyTracking->completed_date;
            //$arr['tracking_name']=$each_data->lastTrack->tracking_process->display_name;
            $arr['phone'] = $each_data->phone;
            $arr['loginTracking'] = Carbon::parse($each_data->loginTracking->completed_date)->format('d-m-Y');
            $arr['cycle_time'] = Carbon::parse($each_data->competencyTracking->completed_date)->diffInDays($each_data->loginTracking->completed_date);
            if ($each_data->lastTrack->process_lookups_id == config('globals.training_completed_tracking_id')) {
                $secondLastTrackingStep = config('globals.training_completed_tracking_id') - 1;
                if ($each_data->tracking->slice(1)->first()->process_lookups_id == $secondLastTrackingStep) {
                    $arr['tracking_name'] = $each_data->lastTrack->tracking_process->display_name ?? '--';
                    $arr['tracking_step_id'] = $each_data->lastTrack->tracking_process->id;
                } else {
                    $arr['tracking_name'] = $each_data->tracking->slice(1)->first()->tracking_process->display_name ?? '--';
                    $arr['tracking_step_id'] = $each_data->tracking->slice(1)->first()->tracking_process->id;
                }
            } else {
                $arr['tracking_name'] = $each_data->lastTrack->tracking_process->display_name ?? '--';
                $arr['tracking_step_id'] = $each_data->lastTrack->tracking_process->id;
            }


            $training_user = TrainingUser::where('model_id', $each_data->id)->select('id')->first();
            if ($training_user != null) {
                $training_user_course = TrainingUserCourseAllocation::where('training_user_id', $training_user->id)
                    ->with('course_with_trashed')
                    ->get()->toArray();
                $course_name = null;
                $completed_percentage = null;
                if (!empty($training_user_course)) {
                    foreach ($training_user_course as $key => $value) {
                        $course_name .=  $value['course_with_trashed']['course_title'] . '#';
                        $completed_percentage .=  $value['completed_percentage'] . '#';
                    }
                }
                $arr['course']  = $course_name;
                $arr['completed_percentage']  = $completed_percentage;
            } else {
                $arr['course']  = '--';
                $arr['completed_percentage']  = '--';
            }

            array_push($datatable_rows, $arr);
        }

        return $datatable_rows;
    }

    /**
     * To get the job application
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @return void
     */
    public function getJobApplicationOfCandidate($candidate_id)
    {
        // $candidateJob = $this->recCandidateJobDetails->with([
        //     'job',
        //     'candidate',
            //     'candidate.attachements',
            //     'candidate.attachements.attachment',
            //     'candidate.addresses',
            //     'candidate.availability',
            //     'candidate.securityclearance',
            //     'candidate.guardingExperience',
            //     'candidate.securityproximity',
            //     'candidate.wageExpectation',
            //     'candidate.experience',
            //     'candidate.miscellaneous',
            //     'candidate.employment_history',
            //     'candidate.references',
            //     'candidate.educations',
            //     'candidate.languages',
            //     'candidate.screening_questions',
            //     'candidate.skills',
            //     'candidate.termination',
            //     'candidate.personality_inventories.question',
            //     'candidate.personality_inventories.answer',
            //     'candidate.personality_sums',
            //     'candidate.personality_scores.score_type',
            //     'candidate.competency_matrix.competency_matrix',
            //     'candidate.competency_matrix.competency_matrix.category',
            //     'candidate.competency_matrix.competency_matrix_rating',
            //     'candidate.comissionaires_understanding',
            // ])
            //     ->where('candidate_id', '=', $candidate_id)
            //    // ->where('status', 'Applied')
            //    // ->where('job_id', '=', $job_id)
            //     ->first();
            // return $candidateJob;
            $candidate = $this->candidateModel->with([
            'addresses',
            'availability',
            'referalAvailibility',
            'awareness',
            'securityclearance',
            'guardingExperience',
            'securityproximity',
            'wageExpectation',
            'experience',
            'miscellaneous',
            'employment_history',
            'references',
            'educations',
            'languages',
            'screening_questions',
            'skills',
            'force',
            'termination',
            'personality_inventories.question',
            'personality_inventories.answer',
            'personality_sums',
            'personality_scores.score_type',
            'competency_matrix.competency_matrix',
            'competency_matrix.competency_matrix.category',
            'competency_matrix.competency_matrix_rating',
            'comissionaires_understanding',
            'candidateShipment'
            ])
            ->with(['tracking' => function ($q) {
                $q->where('process_lookups_id', '=', 10);
            }])
            ->where('id', '=', $candidate_id)
            // ->where('status', 'Applied')
            // ->where('job_id', '=', $job_id)
            ->first();
            return $candidate;
    }

    /**
     * prepare candidate records for to be converted as employee
     * @param tracking_summary
     * @return array
     *
     */
    public function getCandidateConversionList()
    {
        $records = RecCandidate::select('id', 'name', 'email')->where('is_converted', 0)
            ->with(['candidateJobs' => function ($q) {
                $q->where('status', 3)->with(array('job' => function ($query) {
                    $query->select('id', 'unique_key');
                }));
            }])
            ->with('uniformSubmittedTracking')
            ->whereHas('candidateShipment', function ($query) {
                $query->where('shippment_status', '!=', null)->whereHas('shippmentDetailsLog');
            })
            ->get()->toArray();
                    
        /*     dd($records);
        //$latest_record = RecProcessSteps::orderBy('step_order', 'desc')->first();
        $latest_record = RecProcessSteps::where('step_name', 'uniform_received')->first();
        $already_transitioned = RecCandidateEmployee::pluck('candidate_id')->toArray();
        $records = $this->recCandidateTracking
        ->where('process_lookups_id', $latest_record->id)
        ->whereHas('candidate')
        ->with(
        'candidate',
        //'job',
        'tracking_process'
        // 'candidatejob.job' // added for the new table joining
        )
        ->whereNotIn('candidate_id', $already_transitioned)
        //->whereHas('candidatejob')
        //->whereDoesntHave('candidate.termination')
        ->with(['candidate' => function ($query) {
        $query->with(['candidateJobs' => function ($q) {
            $q->where('status', 3)->with('job');
        }]);
        }])
        ->get()->toArray();
        $candidate_id_list = RecCandidateEmployee::pluck('candidate_id')->toArray(); */
        return $this->prepareDataArray($records);
    }

    /**
     * prepare candidate records for conversion
     * @param tracking_summary
     * @return array
     *
     */
    public function prepareDataArray($records)
    {
        $datatable_rows = array();
        $candidate_arr = array();
        foreach ($records as $key => $each_record) {
            if (!in_array($each_record['id'], $candidate_arr)) {
                $candidate_arr[] = $each_record['id'];
                $each_row["id"] = $each_record['id'];
                $each_row["candidate_id"] = $each_record['id'];
                $each_row["completion_date"] = $each_record['uniform_submitted_tracking']['completed_date'];
                $each_row["candidate_name"] = $each_record['name'];
                $each_row["candidate_email"] = $each_record['email'];
                $each_row["job_id"] = $each_record['candidate_jobs']['job']['unique_key'];
                //   $each_row["status"] = in_array($each_record['candidate_id'], $candidate_id_list) ? 0 : 1;
                array_push($datatable_rows, $each_row);
            }
        }

        return $datatable_rows;
    }

    /**
     * Function to get details of a user array
     * @param type $candidate_ids array
     */
    public function getCandidateArrDetails($candidate_ids)
    {

        $candidate_details = $this->candidateModel
            ->with(['awareness', 'experience', 'candidateJobs'])
            ->whereIn('id', $candidate_ids)
            ->get();
        return $candidate_details;
    }

    /**
     * Function to get details of a single user
     * @param type $user_id
     */
    public function getCandidateDetails($candidate_id)
    {

        $user_details = $this->recCandidateAwarenessModel
            ->with(
                'candidate.guardingExperience',
                'candidate.miscellaneous',
                'jobReassigned.customer',
                'candidate.wageExpectation'
            )
            ->where('candidate_id', $candidate_id)
            ->with(['candidate' => function ($query) {
                $query->with(['candidateJobs' => function ($q) {
                    $q->where('status', 3)->with('job.customer');
                }]);
            }])
            ->first();
        return $user_details;
    }

    public function candidateEmployeeMapping($user_store, $candidate_id)
    {
        $employee = new RecCandidateEmployee;
        $employee->candidate_id = $candidate_id;
        $employee->user_id = $user_store->id;
        $employee->updated_by = Auth::user()->id;
        $employee->save();
        return $employee->id;
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
                RecCandidate::where('id', $id)->delete();
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
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
        ->wherehas('latestApplied', function ($query) use ($candidate_selection_status, $user) {
            $query->where('status', '=', 'Applied')
            ->when($candidate_selection_status != null, function ($query) use ($candidate_selection_status) {
                $query->where('candidate_status', '=', $candidate_selection_status);
            });
        })
        // ->wherehas('latestJobApplied.job', function ($query) use ($user) {
        //     $trackAllPermission = $user->can('track_all_candidates') || $user->can('admin') || $user->can('super_admin');
        //     $query->when(!$trackAllPermission, function ($query) use ($user) {
        //         $query->where('hr_rep_id', '=', $user->id)
        //             ->orWhere('user_id', '=', $user->id);
        //     });
        // })
        ->pluck('id');
        return $query;
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
            $this->recCandidateAwarenessModel = $this->recCandidateAwarenessModel->find($request->get('id'));
            $this->recCandidateAwarenessModel->candidate_status = $request->get('candidate_status');
            $this->recCandidateAwarenessModel->feedback_id = $request->get('feedback_id');
            $this->recCandidateAwarenessModel->save();
            if ($request->get('candidate_status') == 'Proceed') {
                $data['id'] = $this->recCandidateAwarenessModel->candidate_id;
                $data['review_completed'] = 1;
                $this->updateCandidateCredentials($data);
                $this->recCandidateTrackingRepository->saveTracking($this->recCandidateAwarenessModel->candidate_id, "screen_applications");
                $candidate = RecCandidate::with('wageExpectation', 'availability', 'awareness', 'guardingExperience', 'miscellaneous')->find($this->recCandidateAwarenessModel->candidate_id);
                if ($candidate->is_converted == 0) {
                    RecMatchScoreCandidate::dispatch($candidate);
                    $helper_variable = array(
                        '{receiverFullName}' => HelperService::sanitizeInput($candidate->name),
                        '{recruiterFullName}' => HelperService::sanitizeInput(\Auth::user()->full_name)
                    );
                    $emailResult = $this->mailQueueRepository
                        ->prepareMailTemplate(
                            "rec_candidate_application_evaluation_acknowledgement",
                            null,
                            $helper_variable,
                            "Modules\Recruitment\Models\RecCandidate",
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            $candidate->id
                        );
                }
            }
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllCandidateCredentials()
    {
        $candidateDetails = $this->candidateModel->with('lastTrack.tracking_process', 'userAccessTracking', 'tracking.tracking_process')->get();
        return $this->prepareCandidateDetailsArr($candidateDetails);
    }
    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllCandidateTrackings()
    {
        $candidateDetails= $this->candidateModel->with('lastTrack.tracking_process', 'userAccessTracking', 'tracking.tracking_process')
        ->when((\Auth::user()->hasPermissionTo('rec-view-allocated-candidates-tracking')), function ($query) {
            $customer_list=$this->customerEmployeeAllocationRepository->getDirectAllocatedCustomersList(\Auth::user());

            $jobList=RecJob::whereIn('customer_id', array_keys($customer_list))->pluck('id')->toArray();
            $query->whereHas('candidateJobs', function ($q) use ($jobList) {
                   $q->whereIn('job_id', $jobList)->where('status', 3);
            });
        })->get();
        return $this->prepareCandidateDetailsArr($candidateDetails);
    }
    
    public function prepareCandidateDetailsArr($data)
    {
        $arr = $datatable_rows = array();
        foreach ($data as $key => $each_data) {
            $arr['id'] = $each_data->id;
            $arr['first_name'] = $each_data->first_name;
            $arr['last_name'] = $each_data->last_name ?? '';
            $arr['city'] = $each_data->city;
            $arr['postal_code'] = $each_data->postal_code;
            $arr['email'] = $each_data->email;
            $arr['phone'] = $each_data->phone;
            if ($each_data->lastTrack->process_lookups_id == config('globals.training_completed_tracking_id')) {
                $secondLastTrackingStep = config('globals.training_completed_tracking_id') - 1;
                if ($each_data->tracking->slice(1)->first()->process_lookups_id == $secondLastTrackingStep) {
                    $arr['last_track'] = $each_data->lastTrack->tracking_process->display_name ?? '--';
                } else {
                    $arr['last_track'] = $each_data->tracking->slice(1)->first()->tracking_process->display_name ?? '--';
                }
            } else {
                $arr['last_track'] = $each_data->lastTrack->tracking_process->display_name ?? '--';
            }

            $arr['user_access_tracking_completed_date'] = isset($each_data->userAccessTracking) ? date_format(date_create($each_data->userAccessTracking->completed_date), 'Y-m-d') : '--';
            $arr['last_login'] = isset($each_data->last_login) ? Carbon::parse($each_data->last_login)->format('Y-m-d') : '--';
            $arr['completed_date'] = isset($each_data->lastTrack) ? Carbon::parse($each_data->lastTrack->completed_date)->format('Y-m-d') : '--';
            // $arr['application_date']=Carbon::parse($each_data->competencyTracking->completed_date)->format('d-m-Y');
            $arr['status'] = $each_data->status == 1 ? 'Active' : 'Inactive';
            $arr['created_at'] = Carbon::parse($each_data->created_at)->format('M d, Y');

            $arr['tracking_step_id'] = $each_data->lastTrack->tracking_process->id;
            array_push($datatable_rows, $arr);
        }
        // dd($datatable_rows);
        return $datatable_rows;
    }


    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function getCandidateCredential($id)
    {
        return $this->candidateModel->find($id);
    }

    public function saveCandidateCredentials($data, $request)
    {
        if (empty($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        } else {
            $data['updated_by'] = Auth::user()->id;
        }

        if (array_key_exists("status", $data) && $data['status'] == 'on') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        if (empty($data['id'])) {
            $data['password'] = bcrypt('password');
        }
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['remember_token'] = Str::random(60);
        $dynamicEmailBody = $data['emailScript'];

        $candidate = $this->candidateModel->updateOrCreate(array('id' => $data['id']), $data);
        $resetToken = $this->generateResetToken($candidate->id);
        //Only for newly added
        if (empty($data['id'])) {
            $helper_variable = array(
            '{receiverFullName}' => HelperService::sanitizeInput($candidate->name),
            '{username}' => HelperService::sanitizeInput($candidate->username),
            '{activationUrl}' => URL::temporarySignedRoute(
                'loginaccess',
                now()->addDays(5),
                [
                    'user' => $candidate->id,
                    'token' => $resetToken
                ]
            ),
            );
            $emailResult = $this->mailQueueRepository
                ->prepareMailTemplate(
                    "rec_candidate_register_email_script",
                    null,
                    $helper_variable,
                    "Modules\Recruitment\Models\RecCandidate",
                    0,
                    0,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $candidate->id,
                    $dynamicEmailBody
                );
            $this->recCandidateTrackingRepository->saveTracking($candidate->id, "login_assigned");
        }
        return $candidate;
    }


    public function updateCandidateCredentials($data)
    {
        if ((isset($data['first_name'])) && (isset($data['last_name']))) {
            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        }
        $candidate = $this->candidateModel->updateOrCreate(array('id' => $data['id']), $data);

        return $candidate;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteCandidateCredential($id)
    {
        return $this->candidateModel->destroy($id);
    }


    public function loginRemainder()
    {
        $notYetloginList = $this->recCandidateTracking->where('process_lookups_id', 1)->whereHas('candidate', function ($q) {
            $q->whereNull('last_login')->where('status', 1);
        })->get();
        if (isset($notYetloginList)) {
            foreach ($notYetloginList as $eachNotYetloginList) {
                $helper_variable = array(
                '{receiverFullName}' => HelperService::sanitizeInput($eachNotYetloginList->candidate->name),
                );
                $emailResult = $this->mailQueueRepository
                    ->prepareMailTemplate(
                        "rec_candidate_login_remainder",
                        null,
                        $helper_variable,
                        "Modules\Recruitment\Models\RecCandidate",
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $eachNotYetloginList->candidate->id
                    );
            }
        }
        return true;
    }

    public function acceptTermsAndConditions()
    {
        $user = \Auth::user();
        $response = $this->candidateModel->where('id', $user->id)->update(['terms_accepted' => 1]);
        return $response;
    }

    public function updateFormComplete($request)
    {
        $id = $request->candidate_id;
        $response = $this->candidateModel->where('id', $id)->update(['is_completed' => 1]);
        $candidate = $this->candidateModel->with('awareness')->find($id);
        $candidateJob = $this->recCandidateAwarenessModel->with(
            'candidate',
            // 'job',
            'candidate.addresses',
            'candidate.availability',
            'candidate.securityclearance',
            'candidate.guardingexperience',
            'candidate.securityproximity',
            'candidate.wageexpectation',
            'candidate.references',
            'candidate.educations',
            'candidate.experience',
            'candidate.miscellaneous',
            'candidate.languages',
            'candidate.languages.language_looukp',
            'candidate.employment_history',
            'candidate.screening_questions',
            'candidate.skills',
            'candidate.skills.skill_lookup',
            'candidate.experience.division',
            'candidate.competencyTracking',
            //  'candidate.interviewnote',
            'candidate.comissionaires_understanding'
        )->find($candidate->awareness->id);
        try {
            $candidate_application_fileId = $this->pdfGenerate($candidateJob);
            $updateCandidateAttachment = $this->candidateModel->where('id', $id)->update(['attachment_pdf_id' => $candidate_application_fileId]);
            $helper_variable = array(
                '{receiverFullName}' => HelperService::sanitizeInput($candidate->createdUser->full_name),
                '{candidate}' => HelperService::sanitizeInput($candidate->name)
            );
            $emailResult = $this->mailQueueRepository
                ->prepareMailTemplate(
                    "rec_candidate_application_process_completed",
                    null,
                    $helper_variable,
                    "Modules\Recruitment\Models\RecCandidate",
                    $candidate->created_by,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $candidate_application_fileId
                );
            $candidate_helper_variable = array(
                '{receiverFullName}' => HelperService::sanitizeInput($candidate->name)
            );
            // $candidate_application_fileId = $this->pdfGenerate($candidateJob);
            $emailCandidateResult = $this->mailQueueRepository->prepareMailTemplate(
                "rec_candidate_application_process_completed_candidate",
                null,
                $candidate_helper_variable,
                "Modules\Recruitment\Models\RecCandidate",
                0,
                0,
                null,
                null,
                null,
                null,
                null,
                $candidate_application_fileId,
                null,
                $candidate->id
            );
        } catch (\Exception $e) {
            \Log::error("Candidate pdf email error : " . $e->getMessage() . ' at ' . $e->getLine() . ' in ' . $e->getFile());
        }
        $trainingUser = new TrainingUser();
        $trainingUser->model_name = 'Modules\Recruitment\Models\RecCandidate';
        $trainingUser->model_id = $candidate->id;
        $trainingUser->save();
        $request->request->add(['team_ids' => json_encode([config('globals.rec_training_id')])]);
        $request->request->add(['employee_ids' =>json_encode([$trainingUser->id])]);
        $request->request->add(['is_recruitment' =>1]);
        $this->employeeAllocationRepository->saveAllocation($request);


        return $response;
    }

    /**
     * PDF generation
     * @param $candidateJob
     * @return $filename
     */
    public function pdfGenerate($candidateJob)
    {
    //        $pdf = PDF::loadHTML('<h1>Blah</h1>')->setWarnings(true)->save('myfile1.pdf');;

        $pdf = PDF::loadView('recruitment::job-application.application', compact('candidateJob'));
    //      $pdf->save('myfile1.pdf');
        $file_path = $this->getAttachmentPath($candidateJob->candidate_id);
        $candidateApplicationFilename = uniqid('candidate_application') . ".pdf";
        // $hashed_name = \Hash::make($candidateApplicationFilename). ".pdf";
        $path = storage_path('app') . $this->directory_seperator . $file_path;
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
        $filename = $this->directory_seperator . $candidateApplicationFilename;
        $pdf->save($path . $filename);
        $attachmntId = $this->attachmentRepo->storeAttachment($candidateApplicationFilename, 'pdf', $candidateApplicationFilename, "pdf", 1, 'rec_candidate_application');
        return $attachmntId;
    }

    /**
     * Function to get Attachment path
     * @param $candidate_id
     * @param $job_id
     * @return string
     */
    public function getAttachmentPath($candidate_id)
    {
        return config('globals.rec_candidate_application') . $this->directory_seperator . $candidate_id;
    }
    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */


    public static function getRecAttachmentPathArrFromFile($file_id)
    {
        $candidate = RecCandidate::where('attachment_pdf_id', $file_id)->first();
        if (isset($candidate)) {
            $candidateID = $candidate->id;
        }
        return array(config('globals.rec_candidate_application'), $candidateID);
    }
    /**
     * Candidate Login
     *
     * @param Request $request
     * @param Response
     */
    public function candidateLogin($data)
    {
        $success = false;
        $remember = false;
        $pass = false; //when candidate is remembered
        try {
            if (!empty($data->remember) && $data->remember == 1) {
                $remember = true;
            } elseif (!empty($data->remember)) {
                $candidate = RecCandidate::where('remember_token', $data->remember)->get();
                if (count($candidate) > 0) {
                    Auth::login($candidate->first());
                    $pass = true;
                }
            }
            if ($pass || auth()->guard('rec_candidate')->attempt(['username' => $data->username, 'password' => $data->password, 'status' => 1], $remember)) {
                config(['auth.guards.api.provider' => 'rec_candidate']);
                if ($pass) {
                    $userId = auth()->user()->id;
                } else {
                    $userId = auth()->guard('rec_candidate')->user()->id;
                }
                $candidate = RecCandidate::select(
                    'id',
                    'email',
                    'first_name',
                    'last_name',
                    'terms_accepted',
                    'is_completed',
                    'review_completed',
                    'dob',
                    'profile_image',
                    'remember_token'
                )->find($userId);
                $lastLogin = RecCandidate::where('id', $userId)->update(['last_login' => Carbon::now()]);
                if (empty($candidate->dob)) {
                    $candidate->profile_image = null;
                }
                $success = $candidate;
                $success['accessToken'] = $candidate->createToken('Recruitment', ['rec_candidate'])->accessToken;
                if ($remember || $pass) {
                    $success['rememberToken'] = $candidate->remember_token;
                }
                $status = 200;
                $msg = 'Success';
                if ($candidate->id) {
                    $step = $this->recCandidateTrackingRepository->getProcessStep($candidate->id);
                    if ($step['next_step'] == 'user_access') {
                        $this->recCandidateTrackingRepository->saveTracking($candidate->id, "user_access");
                        $step = $this->recCandidateTrackingRepository->getProcessStep($candidate->id);
                    }
                    $success['process'] = $step;
                }
            } else {
                $status = 400;
                $msg = 'Invalid email or password.';
            }
        } catch (\Exception $e) {
            $status = 406;
            $msg = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
        }
        return response()->json(['message' => $msg, 'status' => $status, 'success' => $success]);
    }

    /**
     * Get candidate profile api
     */
    public function getCandidateProfile()
    {
        $profile['brand_awareness'] = RecBrandAwareness::orderby('order_sequence', 'asc')->get()
        ->pluck('answer', 'id')->toArray();

        $profile['security_awareness'] = RecSecurityAwareness::orderby('order_sequence', 'asc')->get()
        ->pluck('answer', 'id')->toArray();

        $profile['commissionaries_understanding'] = RecCommissionairesUnderstandingLookup::orderby('order_sequence', 'asc')
        ->get()->pluck('commissionaires_understandings', 'id')->toArray();

        $lookups['positions_lookups'] = PositionLookup::orderBy('position', 'ASC')->get()
        ->pluck('position', 'id')->toArray();

        $lookups['security_provider'] = SecurityProviderLookup::orderBy('security_provider', 'ASC')->get()
        ->pluck('security_provider', 'id')->toArray();

        $lookups['experience_ratings'] = RecRateExperienceLookups::orderby('score', 'desc')
        ->pluck('experience_ratings', 'id')->toArray();


        $skill_collection = SkillLookup::select('id', 'category', 'skills')->get();
        $collection = collect($skill_collection);
        $lookups['skills_lookup'] = $collection->groupBy('category');

        $lookups['smart_phones'] = SmartPhoneType::pluck('type', 'id')->toArray();

        $lookups['division'] = DivisionLookup::pluck('division_name', 'id')->toArray();

        return ['profile' => $profile, 'lookups' => $lookups];
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
     * Delete Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function deleteCandidateAttachment($candidate_id, $attachment_id)
    {
        try {
            RecCandidateAttachment::where(['candidate_id' => $candidate_id, 'attachment_id' => $attachment_id])->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get all candidate name
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function getCandidatesName()
    {
        return RecCandidate::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }

    public function sendPasswordResetMail($data)
    {
        try {
            $candidate = RecCandidate::findOrFail($data['candidate-id']);
            $dynamicEmailBody = $data['password_reset_mail'] ?? null;
            $resetToken = $this->generateResetToken($data['candidate-id']);
            $helper_variable = array(
            '{receiverFullName}' => HelperService::sanitizeInput($candidate->name),
            '{username}' => HelperService::sanitizeInput($candidate->username),
            '{activationUrl}' => URL::temporarySignedRoute(
                'loginaccess',
                now()->addDays(5),
                [
                    'user' => $candidate->id,
                    'token' => $resetToken
                ]
            )
            );
            $emailResult = $this->mailQueueRepository
                ->prepareMailTemplate(
                    "rec_candidate_password_reset",
                    null,
                    $helper_variable,
                    "Modules\Recruitment\Models\RecCandidate",
                    0,
                    0,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $candidate->id,
                    $dynamicEmailBody
                );
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function reviewScreeningAns($data)
    {
        foreach ($data['score'] as $question_id => $score) {
            RecCandidateScreeningQuestion::where(array('candidate_id' => $data['candidate_id'], 'question_id' => $question_id))->update(['score' => $score]);
        }
        $newData['average_score'] = $data['total'];
        $newData['english_rating_id'] = $data['english_rating_id'];
        $newData['interview_score'] =  $data['interview_score'] ?? null;
        $newData['interview_date'] = $data['interview_date'] ?? null;
        $newData['interview_notes'] = $data['interview_notes'] ?? null;
        $newData['reference_score'] = $data['reference_score'] ?? null;
        $newData['reference_date'] = $data['reference_date'] ?? null;
        $newData['reference_notes'] = $data['reference_notes'] ?? null;
        $interviewNoteswithDate = isset($data['interview_notes']) ? ($data['interview_notes'] . "\n" . ' - Date :' . $newData['interview_date']) . "\n" : null;
        $referenceNoteswithDate = isset($data['reference_notes']) ? ($data['reference_notes'] . "\n" . ' - Date :' . $newData['reference_date']) . "\n" : null;
        $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 1)->where('candidate_id', '=', $data['candidate_id'])->first();
        $job_id = isset($job_details) ? $job_details->job_id : null;
        if (isset($data['interview_score']) || isset($data['interview_date'])) {
            $this->recCandidateTrackingRepository->saveTracking($data['candidate_id'], "interview_completed", false, $job_id, $interviewNoteswithDate);
        }
        if (isset($data['reference_score']) || isset($data['reference_date'])) {
            $this->recCandidateTrackingRepository->saveTracking($data['candidate_id'], "references_validated", false, $job_id, $referenceNoteswithDate);
        }
        RecCandidateAwareness::updateOrCreate(array('candidate_id' => $data['candidate_id']), $newData);
        $this->recCandidateTrackingRepository->saveTracking($data['candidate_id'], "rate_screening_questions", 5);
        return $newData;
    }

    public function generateResetToken($candidateId)
    {
        $resetToken = md5(microtime() . 'rec' . $candidateId);
        RecCandidate::where('id', $candidateId)->update(
            ['reset_token' => $resetToken]
        );
        return $resetToken;
    }

    /**
     * Get the path including file name to incident report attachment
     * @param $incident_report_id
     * @return string
     */
    public function forceAttachment($file_id)
    {
        $path = array();
        $force_attachment = RecCandidateForceCertification::where('attachment_id', $file_id)->first();
        $candidate_id = $force_attachment->candidate_id;
        $file_name = $force_attachment->forceAttachmentDetails->hash_name;
        if (!empty($force_attachment->attachment_id)) {
            $path['path'] = storage_path('app') . $this->directory_seperator . config('globals.candidate_recruitment') . $this->directory_seperator . $candidate_id . $this->directory_seperator . $file_name;
            $path['file'] = $file_name;
        }
        return $path;
    }
    public function getCandidatesById($candidate_id_arr)
    {
        return $this->getCandidates(null, null, null, 'name', null, null, $candidate_id_arr);
    }

    public function prepareCandidatesExcel($results, $customerLoc = null)
    {
        $result_arr = [];
        ////////////Header/////////////////////////
        $header_arr = array(
        // "Client Name",
        "Candidate Name",
        "Gender",
        "City",
        "Postal Code",
        "Wage Expectation",
        // "Date Applied",
        "Email Address",
        "Phone",
        "Date of Birth",
        // "Status",
        // "Overall Impression",
        // "Position Code",
        // "Wage Per Hour",
        "Orientation",
        "Job Post Finding",
        "Sponsor Email",
        "Willingness to work on other positions",
        "How many hours a week are you looking for",
        "How soon could you start?",
        "Had you heard about Commissionaires?",
        "how familiar are you with Garda, G4S,Securitas or Palladin?",
        "How many hours per week would you prefer to work?",
        "Please share your understanding of Commissionaires PRIOR to applying ",
        // "Please elaborate why you are applying for this specific role, and why you think you would succeed in the role",
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
        "Years of experience in Area Manager",
        "Years of experience in Bylaw Officer",
        "Years of experience in CCTV Operator",
        "Years of experience in Client Manager",
        "Years of experience in Concierge",
        "Years of experience in Crossing Guard",
        "Years of experience in Dispatch",
        "Years of experience in Foot Patrol",
        "Years of experience in Investigations",
        "Years of experience in Loss Prevention Officer",
        "Years of experience in Mobile Patrols",
        "Years of experience in Operations",
        "Years of experience in Security Guard",
        "Years of experience in Shift Leader",
        "Years of experience in Site Supervisor",
        "Years of experience in Spares",
        "Years of experience in Other",
        "Wage expectations",
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
        $references_count = max(array_map('count', data_get($results, '*.candidate.references')));
        $employment_count = max(array_map('count', data_get($results, '*.candidate.employment_history')));
        $education_count = max(array_map('count', data_get($results, '*.candidate.educations')));
        $skills_obj = (data_get($results, '*.candidate.skills'));

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
            // if (isset($customerLoc)) {
            //     $origins["lat"] = $value->geo_location_lat;
            //     $origins["long"] = $value->geo_location_long;
            //     $destinations["lat"] = $customerLoc->geo_location_lat;
            //     $destinations["long"] = $customerLoc->geo_location_long;
            //     $locationArr = array(
            //         "origins" => [$origins],
            //         "destinations" => [$destinations],
            //     );
            //     $distObj = $this->locationService
            //         ->getDrivingDistance($locationArr, true);
            //     if ($distObj["status"]) {
            //         $dist["time"] = $distObj["distanceMatrix"]->duration->value / 60; //converted to minutes
            //     } else {
            //         $dist["time"] = "";
            //     }
            // }

            $wage_position = 'Other';
            $guard_position = '';
            $wage_provider_other = '';
            $availability_ex = '';
            $availability_shift_ex = '';
            $clearance_ex = '';
            $recurity_transport_ex = '';

            $dec_position_ex = isset($value->candidate->guardingexperience->positions_experinces) ? json_decode($value->candidate->guardingexperience->positions_experinces) : [];
            $languare_obj = (data_get($value->candidate, 'languages'));
            $skills_obj = (data_get($value->candidate, 'skills'));
            $skill_lookups = ((data_get($skills_obj, '*.skill_lookup', 0)));
            $days = $value->candidate->availability->days_required ?? [];
            $shift = $value->candidate->availability->shifts ?? [];

            if (isset($value->candidate->guardingexperience->social_insurance_number)) {
                if ($value->candidate->guardingexperience->social_insurance_number != null && $value->candidate->guardingexperience->social_insurance_number == 1) {
                    $social_no = 'Yes';
                } elseif ($value->candidate->guardingexperience->social_insurance_number != null && $value->candidate->guardingexperience->social_insurance_number == 0) {
                    $social_no = 'No';
                } else {
                    $social_no = '';
                }
            } else {
                $social_no = '';
            }

            if (isset($value->candidate->guardingexperience->sin_expiry_date_status)) {
                if ($value->candidate->guardingexperience->sin_expiry_date_status != null && $value->candidate->guardingexperience->sin_expiry_date_status == 1) {
                    $sin_status = 'Yes';
                } elseif ($value->candidate->guardingexperience->sin_expiry_date_status != null && $value->candidate->guardingexperience->sin_expiry_date_status == 0) {
                    $sin_status = 'No';
                } else {
                    $sin_status = '';
                }
            } else {
                $sin_status = '';
            }

            if (isset($value->candidate->wageexpectation->last_role_held)) {
                $wage_position = ($value->candidate->wageexpectation->last_role_held > 0) ? $value->candidate->wageexpectation->lastrole->position : '';
            }
            if (isset($value->candidate->guardingexperience->most_senior_position_held)) {
                $guard_position = ($value->candidate->guardingexperience->most_senior_position_held > 0) ? $value->candidate->guardingexperience->position['position'] : 'Other';
            }
            if (isset($value->candidate->wageexpectation->wageprovider->security_provider)) {
                $wage_provider_other = ($value->candidate->wageexpectation->wageprovider->security_provider == 'Other') ? $value->candidate->wageexpectation->wage_last_provider_other : '';
            }
            if (isset($value->candidate->availability->current_availability)) {
                $availability_ex = ($value->candidate->availability->current_availability == "Part-Time (Less than 40 hours per week)") ? $value->candidate->availability->availability_explanation : '';
            }
            if (isset($value->candidate->availability->available_shift_work)) {
                $availability_shift_ex = ($value->candidate->availability->available_shift_work == "No") ? $value->candidate->availability->explanation_restrictions : '';
            }
            if (isset($value->candidate->securityclearance->no_clearance)) {
                $clearance_ex = ($value->candidate->securityclearance->no_clearance == "Yes") ? $value->candidate->securityclearance->no_clearance_explanation : '';
            }
            if (isset($value->candidate->securityproximity->transportation_limitted)) {
                $recurity_transport_ex = ($value->candidate->securityproximity->transportation_limitted == "Yes") ? $value->candidate->securityproximity->explanation_transport_limit : '';
            }
            if ($value->candidate->referalAvailibility !== null && $value->candidate->referalAvailibility->orientation !== null) {
                // $referalAvailablityOrientation = ($value->candidate->referalAvailibility->orientation != 0) ? 'Yes' : 'No';
                $referalAvailablityOrientation = $value->candidate->referalAvailibility->orientation ?? '';
            } else {
                $referalAvailablityOrientation = '';
            }

            if ($value->candidate->gender == 1) {
                $gender = 'Male';
            } elseif ($value->candidate->gender == 2) {
                $gender = 'Female';
            } else {
                $gender = 'Others';
            }

            $body_arr = array(
            //$value->latestJobApplied->job->customer->client_name ?? '',
            $value->candidate->name ?? '',
            $gender ?? '',
            $value->candidate->city ?? '',
            $value->candidate->postal_code ?? '',
            $value->candidate->wageexpectation->wage_expectations ?? '',
            // $value->latestJobApplied->created_at ?? '',
            $value->candidate->email ?? '',
            $value->candidate->phone . ',' . $value->candidate->phone_cellular,
            $value->candidate->dob ?? '',
            // ($value->latestJobApplied->candidate_status != '') ? $value->latestJobApplied->candidate_status : 'Not Set',
            // $value->latestJobApplied->feedback->feedback ?? '',
            // $value->latestJobApplied->job->unique_key ?? '',
            // $value->latestJobApplied->job->wage_low . '-' . $value->latestJobApplied->job->wage_high,
            $referalAvailablityOrientation,
            $value->candidate->referalAvailibility->jobPostFinding->job_post_finding ?? '',
            $value->candidate->referalAvailibility->sponser_email ?? '',
            $value->candidate->referalAvailibility ? config('globals.position_availibility')[$value->candidate->referalAvailibility->position_availibility] : '',
            $value->candidate->referalAvailibility->floater_hours ?? '',
            $value->candidate->referalAvailibility ? config('globals.starting_time')[$value->candidate->referalAvailibility->starting_time] : '',
            $value->candidate_brand_awareness->answer ?? '',
            $value->candidate_security_awareness->answer ?? '',
            $value->prefered_hours_per_week ?? '',
            $value->candidate->comissionaires_understanding[0]->candidateUnderstandingLookup->commissionaires_understandings ?? '',
            // $value->latestJobApplied->fit_assessment_why_apply_for_this_job ?? '',
            $value->candidate->address ?? '',
            $value->candidate->guardingexperience->guard_licence ?? '',
            $value->candidate->guardingexperience->start_date_guard_license ?? '',
            $value->candidate->guardingexperience->start_date_first_aid ?? '',
            $value->candidate->guardingexperience->start_date_cpr ?? '',
            $value->candidate->guardingexperience->expiry_guard_license ?? '',
            $value->candidate->guardingexperience->expiry_first_aid ?? '',
            $value->candidate->guardingexperience->expiry_cpr ?? '',
            $value->candidate->guardingexperience->test_score_percentage ?? '',
            $value->candidate->guardingexperience->security_clearance ?? '',
            $value->candidate->guardingexperience->security_clearance_type ?? '',
            $value->candidate->guardingexperience->security_clearance_expiry_date ?? '',
            $social_no,
            $sin_status,
            $value->candidate->guardingexperience->sin_expiry_date ?? '',
            $value->candidate->guardingexperience->years_security_experience ?? '',
            $guard_position ?? 'Other',
            $dec_position_ex->access_control ?? '',
            $dec_position_ex->area_manager ?? '',
            $dec_position_ex->bylaw_officer ?? '',
            $dec_position_ex->cctv_operator ?? '',
            $dec_position_ex->client_manager ?? '',
            $dec_position_ex->concierge ?? '',
            $dec_position_ex->crossing_guard ?? '',
            $dec_position_ex->dispatch ?? '',
            $dec_position_ex->foot_patrol ?? '',
            $dec_position_ex->investigations ?? '',
            $dec_position_ex->loss_prevention_officer ?? '',
            $dec_position_ex->mobile_patrols ?? '',
            $dec_position_ex->operations ?? '',
            $dec_position_ex->security_guard ?? '',
            $dec_position_ex->shift_leader ?? '',
            $dec_position_ex->site_supervisor ?? '',
            $dec_position_ex->spares ?? '',
            $dec_position_ex->other ?? '',
            $value->candidate->wageexpectation->wage_expectations ?? '',
            $value->candidate->wageexpectation->wage_last_hourly ?? '',
            $value->candidate->wageexpectation->wage_last_hours_per_week ?? '',
            $value->candidate->wageexpectation->current_paystub ?? '',
            $value->candidate->wageexpectation->wageprovider->security_provider ?? '',
            $wage_provider_other ?? '',
            $value->candidate->wageexpectation->security_provider_strengths ?? '',
            $value->candidate->wageexpectation->security_provider_notes ?? '',
            $value->candidate->wageexpectation->rating->experience_ratings ?? '',
            $wage_position ?? 'Other',
            $value->candidate->wageexpectation->explanation_wage_expectation ?? '',
            $value->candidate->availability->availability_start ?? '',
            $value->candidate->availability->current_availability ?? '',
            $availability_ex ?? '',
            $days,
            $shift,
            $value->candidate->availability->understand_shift_availability ?? '',
            $value->candidate->availability->available_shift_work ?? '',
            $availability_shift_ex ?? '',
            $value->candidate->securityclearance->born_outside_of_canada ?? '',
            $value->candidate->securityclearance->work_status_in_canada ?? '',
            $value->candidate->securityclearance->years_lived_in_canada ?? '',
            $value->candidate->securityclearance->prepared_for_security_screening ?? '',
            $value->candidate->securityclearance->no_clearance ?? '',
            $clearance_ex ?? '',
            $value->candidate->securityproximity->driver_license ?? '',
            $value->candidate->securityproximity->access_vehicle ?? '',
            $value->candidate->securityproximity->access_public_transport ?? '',
            $value->candidate->securityproximity->transportation_limitted ?? '',
            $recurity_transport_ex ?? '',

            );

            ////// Employment History ////////
            $emp_arr = array();

            foreach ($value->candidate->employment_history as $key => $emp) {
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
            $total_count = count($value->candidate->employment_history);
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
            foreach ($value->candidate->references as $key => $ref) {
                array_push(
                    $ref_arr,
                    $ref->reference_name,
                    $ref->reference_employer,
                    $ref->reference_position,
                    $ref->contact_phone,
                    $ref->contact_email
                );
            }
            $total_ref_count = count($value->candidate->references);
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
            foreach ($value->candidate->educations as $key => $edu) {
                array_push(
                    $edu_arr,
                    $edu->start_date_education,
                    $edu->end_date_education,
                    $edu->grade,
                    $edu->program,
                    $edu->school
                );
            }
            $total_edu_count = count($value->candidate->educations);
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
            (isset($languare_obj[0]) && $languare_obj[0]->language_id == 1) ? $languare_obj[0]->speaking : '',
            (isset($languare_obj[0]) && $languare_obj[0]->language_id == 1) ? $languare_obj[0]->reading : '',
            (isset($languare_obj[0]) && $languare_obj[0]->language_id == 1) ? $languare_obj[0]->writing : '',
            (isset($languare_obj[1]) && $languare_obj[1]->language_id == 2) ? $languare_obj[1]->speaking : '',
            (isset($languare_obj[1]) && $languare_obj[1]->language_id == 2) ? $languare_obj[1]->reading : '',
            (isset($languare_obj[1]) && $languare_obj[1]->language_id == 2) ? $languare_obj[1]->writing : '',
            );
            foreach ($skills_obj as $skills) {
                array_push($skill_arr, $skills->skill_level);
            }

            $other_arr = array(
            $value->candidate->smart_phone_type_id ? 'Yes' : 'No',
            $value->candidate->technicalSummary->type ?? '',
            $value->candidate->smart_phone_skill_level ?? '',
            $value->candidate->experience->current_employee_commissionaries ?? '',
            $value->candidate->experience->employee_number ?? '',
            $value->candidate->experience->currently_posted_site ?? '',
            $value->candidate->experience->position ?? '',
            $value->candidate->experience->hours_per_week ?? '',
            $value->candidate->experience->applied_employment ?? '',
            $value->candidate->experience->start_date_position_applied ?? '',
            $value->candidate->experience->end_date_position_applied ?? '',
            $value->candidate->experience->position_applied ?? '',
            $value->candidate->experience->employed_by_corps ?? '',
            $value->candidate->experience->position_employed ?? '',
            $value->candidate->experience->start_date_employed ?? '',
            $value->candidate->experience->end_date_employed ?? '',
            $value->candidate->experience->division->division_name ?? '',
            $value->candidate->experience->employee_num ?? '',
            $value->candidate->miscellaneous->veteran_of_armedforce ?? '',
            $value->candidate->miscellaneous->service_number ?? '',
            $value->candidate->miscellaneous->canadian_force ?? '',
            $value->candidate->miscellaneous->enrollment_date ?? '',
            $value->candidate->miscellaneous->release_date ?? '',
            $value->candidate->miscellaneous->item_release_number ?? '',
            $value->candidate->miscellaneous->rank_on_release ?? '',
            $value->candidate->miscellaneous->military_occupation ?? '',
            $value->candidate->miscellaneous->reason_for_release ?? '',
            $value->candidate->miscellaneous->spouse_of_armedforce ?? '',
            $value->candidate->miscellaneous->is_indian_native ?? '',
            $value->candidate->miscellaneous->dismissed ?? '',
            $value->candidate->miscellaneous->explanation_dismissed ?? '',
            $value->candidate->miscellaneous->limitations ?? '',
            $value->candidate->miscellaneous->limitation_explain ?? '',
            $value->candidate->miscellaneous->criminal_convicted ?? '',
            $value->candidate->miscellaneous->offence ?? '',
            $value->candidate->miscellaneous->offence_date ?? '',
            $value->candidate->miscellaneous->offence_location ?? '',
            $value->candidate->miscellaneous->career_interest ?? '',
            $value->candidate->miscellaneous->other_roles ?? '',

            );

            // Average screening questions score
            $scoreArr = array();
            $scoreArr[] = $value->average_score;
            $scoreArr[] = isset($value->personality_scores[0])
                ? $value->personality_scores[0]->score
                : "";

            // Competency matrix rating
            $competencyArr = array();
            foreach ($competencyHeadersOrderArr as $competencyId) {
                $competencyArr[] = isset($value->candidate->competency_matrix
                    ->where('competency_matrix_lookup_id', $competencyId)
                    ->first()->competency_matrix_rating->rating)
                    ? $value->candidate->competency_matrix
                    ->where('competency_matrix_lookup_id', $competencyId)
                    ->first()->competency_matrix_rating->rating
                    : '';
            }
            $otherLanguageArray = $value->candidate->other_languages;
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

            // if (isset($customerLoc)) {
            //     $drivingArr = array($dist['time']);
            // }

            $interviewScoreArr = array(
            $value->interview_score ?? "",
            $value->reference_score ?? "",
            );

            $useOfForceArr = array(
                $value->candidate->force->force ?? '',
                $value->candidate->force->force_lookup->use_of_force ?? '',
                $value->candidate->force->expiry ?? '',
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
            // if (isset($customerLoc)) {
            //     $res_arr1 = array_merge($res_arr1, $drivingArr);
            // }
            $res_arr1 = array_merge($res_arr1, $interviewScoreArr);
            $res_arr1 = array_merge($res_arr1, $useOfForceArr);
            $final_arr[] = $res_arr1;
        }

        $final_header[] = $header_arr1;
        $result_arr = array_merge($final_header, $final_arr);
        return $result_arr;
    }

    public function get($id)
    {
        return $this->candidateModel->find($id);
    }
    public function getCandidateComparisonList($candidate_ids)
    {
        $query = $this->candidateModel
        ->with([
            'guardingExperience',
            'wageExpectation',
            'availability',
            'securityclearance',
            'securityproximity',
            'languages',
            'skills.skill_lookup',
            'miscellaneous',
            'referalAvailibility',
            'competency_matrix.competency_matrix_rating',
            'awareness',
        ])
        ->whereIn('id', $candidate_ids)->get();
         return $this->preparedCandidateComparisonArray($query);
    }

    public function preparedCandidateComparisonArray($data)
    {
        $arr  = array();
        $starting_time = [null => '--', "1" => "less than 1", "2" => "1-3", "3" => "3-5", "4" => "5-10 "];
        $now = Carbon::now()->toDateString();
        foreach ($data as $key => $each_data) {
            $arr['name'][$key] = $each_data->name;
            $arr['Profile Image'][$key] = $each_data->profile_image ?? '--';
            $arr['Years of Security Experience'][$key] = $each_data->guardingExperience->years_security_experience;
            $arr['Most Senior Post'][$key] = ($each_data->guardingexperience->most_senior_position_held > 0) ? $each_data->guardingexperience->position['position'] : 'Other';
            $arr['Security License Expiry'][$key] = Carbon::parse($now)->diffInDays($each_data->guardingExperience->security_clearance_expiry_date, true);
            $arr['First Aid Expiry'][$key] = Carbon::parse($now)->diffInDays($each_data->guardingExperience->expiry_first_aid, true);
            $arr['CPR Expiry'][$key] = Carbon::parse($now)->diffInDays($each_data->guardingExperience->expiry_cpr, true);
            $arr['Availability'][$key] = substr($each_data->availability->current_availability, 0, strpos($each_data->availability->current_availability, "("));
            $arr['Status in Canada'][$key] = $each_data->securityclearance->work_status_in_canada;
            $arr['Years in Canada'][$key] = $each_data->securityclearance->years_lived_in_canada;
            $arr['Drivers License'][$key] = $each_data->securityproximity->driver_license;
            $arr['English Oral'][$key] = $each_data->languages[0]['speaking'];
            $arr['English Reading'][$key] = $each_data->languages[0]['reading'];
            $arr['English Writing'][$key] = $each_data->languages[0]['writing'];
            $arr['French Oral'][$key] = $each_data->languages[1]['speaking'];
            $arr['French Reading'][$key] = $each_data->languages[1]['reading'];
            $arr['French Writing'][$key] = $each_data->languages[1]['writing'];

            foreach ($each_data->skills as $eachskill) {
                $arr[$eachskill->skill_lookup->skills][$key] = $eachskill->skill_level;
            }
            $arr['Smart Phone Proficency'][$key] = $each_data->smart_phone_skill_level;
            $arr['Military Vet'][$key] = $each_data->miscellaneous->veteran_of_armedforce ?? '';
            $arr['Criminal Offence'][$key] = $each_data->miscellaneous->criminal_convicted ?? '';
            $arr['Career Interest'][$key] = $each_data->miscellaneous->career_interest ?? '';
            $arr['Case Study Score'][$key] = $each_data->awareness->average_score ?? '';
            $arr['Personality'][$key] = isset($each_data->personality_scores[0]) ? $each_data->personality_scores[0]->score : "";
            $arr['Attend Orientation'][$key] = $each_data->referalAvailibility->orientation;
            $arr['Start As Spare'][$key] = ($each_data->referalAvailibility->position_availibility) == 1 ? 'Yes' : 'No';
            $arr['When Available (days)'][$key] =  $starting_time[$each_data->referalAvailibility->starting_time];
            $arr['Current Wage'][$key] =  $each_data->wageExpectation->wage_last_hourly ?? '';
        }
        return $arr;
    }
}
